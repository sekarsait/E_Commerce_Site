<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dropshipping_controller
 *
 * Seller-facing dropshipping dashboard.
 * Routes (add to application/config/routes.php in the existing loop):
 *
 *   $route[$key . 'dashboard/dropshipping']['GET']                        = 'dropshipping_controller/index';
 *   $route[$key . 'dashboard/dropshipping/suppliers']['GET']              = 'dropshipping_controller/suppliers';
 *   $route[$key . 'dashboard/dropshipping/add-supplier']['GET']           = 'dropshipping_controller/add_supplier';
 *   $route[$key . 'dashboard/dropshipping/import-products']['GET']        = 'dropshipping_controller/import_products';
 *   $route[$key . 'dashboard/dropshipping/fulfillments']['GET']           = 'dropshipping_controller/fulfillments';
 *   $route[$key . 'dashboard/dropshipping/margin-rules']['GET']           = 'dropshipping_controller/margin_rules';
 *
 * Drop this file into: application/controllers/Dropshipping_controller.php
 */
class Dropshipping_controller extends Home_Core_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Must be logged in
        if (!$this->auth_check) {
            redirect(generate_url('register'));
        }

        $this->load->model('dropshipping_model');
    }

    // -------------------------------------------------------------------------
    // Dashboard overview
    // -------------------------------------------------------------------------

    public function index()
    {
        $data['title'] = 'Dropshipping Dashboard';
        $data['stats'] = $this->dropshipping_model->get_stats($this->auth_user->id);
        $data['suppliers'] = $this->dropshipping_model->get_suppliers($this->auth_user->id);

        // Recent fulfillments
        $supplier_ids = array_column($data['suppliers'], 'id');
        if (!empty($supplier_ids)) {
            $data['recent_fulfillments'] = $this->db
                ->where_in('supplier_id', $supplier_ids)
                ->order_by('created_at', 'DESC')
                ->limit(10)
                ->get('dropship_fulfillments')
                ->result();
        } else {
            $data['recent_fulfillments'] = [];
        }

        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/index', $data);
        $this->load->view('partials/_footer');
    }

    // -------------------------------------------------------------------------
    // Suppliers
    // -------------------------------------------------------------------------

    public function suppliers()
    {
        $data['title'] = 'My Suppliers';
        $data['suppliers'] = $this->dropshipping_model->get_suppliers($this->auth_user->id);

        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/suppliers', $data);
        $this->load->view('partials/_footer');
    }

    public function add_supplier()
    {
        $data['title'] = 'Add Supplier';

        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/add_supplier', $data);
        $this->load->view('partials/_footer');
    }

    public function save_supplier()
    {
        post_method();

        $this->form_validation->set_rules('name', 'Supplier name', 'required|trim');
        $this->form_validation->set_rules('api_type', 'API type', 'required|in_list[aliexpress,cj,woocommerce,custom]');
        $this->form_validation->set_rules('api_key', 'API key', 'required|trim');
        $this->form_validation->set_rules('default_margin', 'Default margin', 'required|decimal|greater_than[0]');

        if ($this->form_validation->run()) {
            $id = $this->dropshipping_model->add_supplier([
                'name'           => $this->input->post('name', true),
                'api_type'       => $this->input->post('api_type', true),
                'api_url'        => $this->input->post('api_url', true),
                'api_key'        => $this->input->post('api_key', true),
                'api_secret'     => $this->input->post('api_secret', true),
                'default_margin' => $this->input->post('default_margin', true),
                'currency'       => $this->input->post('currency', true) ?: 'USD',
            ]);

            if ($id) {
                $this->session->set_flashdata('success', 'Supplier added successfully.');
                redirect(generate_url('dashboard') . '/dropshipping/suppliers');
            }
        }

        $data['title'] = 'Add Supplier';
        $data['validation_errors'] = validation_errors();
        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/add_supplier', $data);
        $this->load->view('partials/_footer');
    }

    public function delete_supplier($id)
    {
        $supplier = $this->dropshipping_model->get_supplier($id);
        if ($supplier && $supplier->user_id == $this->auth_user->id) {
            $this->dropshipping_model->delete_supplier($id);
            $this->session->set_flashdata('success', 'Supplier removed.');
        }
        redirect(generate_url('dashboard') . '/dropshipping/suppliers');
    }

    // -------------------------------------------------------------------------
    // Product Import
    // -------------------------------------------------------------------------

    public function import_products()
    {
        $data['title'] = 'Import Products';
        $data['suppliers'] = $this->dropshipping_model->get_suppliers($this->auth_user->id);

        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/import_products', $data);
        $this->load->view('partials/_footer');
    }

    /**
     * AJAX: Search supplier product catalog
     * POST: supplier_id, query
     */
    public function search_supplier_products()
    {
        post_method();
        $supplier_id = (int)$this->input->post('supplier_id', true);
        $query       = $this->input->post('query', true);

        $supplier = $this->dropshipping_model->get_supplier($supplier_id);
        if (!$supplier || $supplier->user_id != $this->auth_user->id) {
            echo json_encode(['success' => false, 'message' => 'Invalid supplier']);
            return;
        }

        $results = $this->search_catalog($supplier, $query);
        echo json_encode(['success' => true, 'products' => $results]);
    }

    /**
     * AJAX: Import a specific product from supplier
     * POST: supplier_id, external_product_id, margin_percent (optional)
     */
    public function do_import_product()
    {
        post_method();
        $supplier_id          = (int)$this->input->post('supplier_id', true);
        $external_product_id  = $this->input->post('external_product_id', true);
        $margin               = $this->input->post('margin_percent', true);

        $supplier = $this->dropshipping_model->get_supplier($supplier_id);
        if (!$supplier || $supplier->user_id != $this->auth_user->id) {
            echo json_encode(['success' => false, 'message' => 'Invalid supplier']);
            return;
        }

        // Fetch full product details from supplier
        $product_data = $this->fetch_supplier_product($supplier, $external_product_id);
        if (empty($product_data)) {
            echo json_encode(['success' => false, 'message' => 'Could not fetch product from supplier']);
            return;
        }

        $applicable_margin = $margin
            ? (float)$margin
            : $this->dropshipping_model->get_applicable_margin($supplier_id, $product_data['supplier_price']);

        $product_id = $this->dropshipping_model->import_product($supplier_id, $product_data, $applicable_margin);

        if ($product_id) {
            echo json_encode([
                'success'    => true,
                'product_id' => $product_id,
                'message'    => 'Product imported. It will be visible after admin approval.',
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Import failed. Check supplier credentials.']);
        }
    }

    // -------------------------------------------------------------------------
    // Fulfillments
    // -------------------------------------------------------------------------

    public function fulfillments()
    {
        $data['title'] = 'Fulfillments';
        $supplier_ids = array_column($this->dropshipping_model->get_suppliers($this->auth_user->id), 'id');

        $data['fulfillments'] = [];
        if (!empty($supplier_ids)) {
            $data['fulfillments'] = $this->db
                ->select('df.*, o.order_number, op.product_id')
                ->from('dropship_fulfillments df')
                ->join('orders o', 'o.id = df.order_id')
                ->join('order_products op', 'op.id = df.order_product_id')
                ->where_in('df.supplier_id', $supplier_ids)
                ->order_by('df.created_at', 'DESC')
                ->get()
                ->result();
        }

        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/fulfillments', $data);
        $this->load->view('partials/_footer');
    }

    // -------------------------------------------------------------------------
    // Margin Rules
    // -------------------------------------------------------------------------

    public function margin_rules()
    {
        $data['title'] = 'Margin Rules';
        $data['suppliers'] = $this->dropshipping_model->get_suppliers($this->auth_user->id);

        $supplier_ids = array_column($data['suppliers'], 'id');
        $data['rules'] = [];
        if (!empty($supplier_ids)) {
            $data['rules'] = $this->db
                ->where_in('supplier_id', $supplier_ids)
                ->where('status', 1)
                ->order_by('min_price', 'ASC')
                ->get('dropship_margin_rules')
                ->result();
        }

        $this->load->view('partials/_header', $data);
        $this->load->view('dashboard/dropshipping/margin_rules', $data);
        $this->load->view('partials/_footer');
    }

    public function save_margin_rule()
    {
        post_method();
        $supplier_id = (int)$this->input->post('supplier_id', true);
        $supplier    = $this->dropshipping_model->get_supplier($supplier_id);

        if (!$supplier || $supplier->user_id != $this->auth_user->id) {
            $this->session->set_flashdata('error', 'Invalid supplier.');
            redirect(generate_url('dashboard') . '/dropshipping/margin-rules');
            return;
        }

        $this->dropshipping_model->save_margin_rule([
            'supplier_id'    => $supplier_id,
            'label'          => $this->input->post('label', true),
            'min_price'      => (float)$this->input->post('min_price', true),
            'margin_percent' => (float)$this->input->post('margin_percent', true),
        ]);

        $this->session->set_flashdata('success', 'Margin rule saved.');
        redirect(generate_url('dashboard') . '/dropshipping/margin-rules');
    }

    // -------------------------------------------------------------------------
    // Private: Catalog search per supplier type
    // -------------------------------------------------------------------------

    private function search_catalog($supplier, $query)
    {
        switch ($supplier->api_type) {
            case 'aliexpress':
                $url = 'https://api.dsers.com/api/v1/products/search?q=' . urlencode($query);
                $r   = $this->http_get($url, ['Authorization' => 'Bearer ' . $supplier->api_key]);
                return $r['data']['products'] ?? [];

            case 'cj':
                $url = 'https://developers.cjdropshipping.com/api2.0/v1/product/list?productName=' . urlencode($query);
                $r   = $this->http_get($url, ['CJ-Access-Token' => $supplier->api_key]);
                return $r['data']['list'] ?? [];

            case 'woocommerce':
                $url = rtrim($supplier->api_url, '/') . '/wp-json/wc/v3/products?search=' . urlencode($query);
                return $this->http_get($url, [], $supplier->api_key, $supplier->api_secret) ?: [];

            default:
                return [];
        }
    }

    private function fetch_supplier_product($supplier, $external_product_id)
    {
        switch ($supplier->api_type) {
            case 'cj':
                $url = 'https://developers.cjdropshipping.com/api2.0/v1/product/query?pid=' . $external_product_id;
                $r   = $this->http_get($url, ['CJ-Access-Token' => $supplier->api_key]);
                $d   = $r['data'] ?? null;
                if (!$d) return null;
                return [
                    'external_product_id' => $external_product_id,
                    'title'               => $d['productNameEn'],
                    'description'         => $d['description'] ?? '',
                    'supplier_price'      => (float)$d['sellPrice'],
                    'images'              => [],
                ];

            case 'woocommerce':
                $url = rtrim($supplier->api_url, '/') . '/wp-json/wc/v3/products/' . $external_product_id;
                $d   = $this->http_get($url, [], $supplier->api_key, $supplier->api_secret);
                if (!$d) return null;
                return [
                    'external_product_id' => $external_product_id,
                    'title'               => $d['name'],
                    'description'         => strip_tags($d['description'] ?? ''),
                    'supplier_price'      => (float)$d['price'],
                    'images'              => array_column($d['images'] ?? [], 'src'),
                ];

            default:
                return null;
        }
    }

    private function http_get($url, $headers = [], $basic_user = null, $basic_pass = null)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => array_map(fn($k, $v) => "$k: $v", array_keys($headers), $headers),
        ]);
        if ($basic_user) {
            curl_setopt($ch, CURLOPT_USERPWD, "$basic_user:$basic_pass");
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true) ?? [];
    }
}
