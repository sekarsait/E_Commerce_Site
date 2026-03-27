<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dropshipping_model
 *
 * Handles all dropshipping operations:
 * - Supplier management
 * - Product import with margin rules
 * - Auto-fulfillment on order
 * - Tracking sync via cron
 *
 * Drop this file into: application/models/Dropshipping_model.php
 */
class Dropshipping_model extends CI_Model
{
    // -------------------------------------------------------------------------
    // SUPPLIERS
    // -------------------------------------------------------------------------

    /**
     * Add a supplier (AliExpress, CJ Dropshipping, custom CSV, etc.)
     */
    public function add_supplier($data)
    {
        $insert = [
            'user_id'        => $this->auth_user->id,
            'name'           => $data['name'],
            'api_type'       => $data['api_type'],   // aliexpress | cj | woocommerce | custom
            'api_url'        => $data['api_url'],
            'api_key'        => $data['api_key'],
            'api_secret'     => isset($data['api_secret']) ? $data['api_secret'] : '',
            'default_margin' => isset($data['default_margin']) ? (float)$data['default_margin'] : 30.00,
            'currency'       => isset($data['currency']) ? $data['currency'] : 'USD',
            'status'         => 1,
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('dropship_suppliers', $insert);
        return $this->db->insert_id();
    }

    public function get_suppliers($user_id = null)
    {
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        return $this->db->get('dropship_suppliers')->result();
    }

    public function get_supplier($id)
    {
        return $this->db->get_where('dropship_suppliers', ['id' => $id])->row();
    }

    public function update_supplier($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('dropship_suppliers', $data);
    }

    public function delete_supplier($id)
    {
        $this->db->where('id', $id);
        return $this->db->update('dropship_suppliers', ['status' => 0]);
    }

    // -------------------------------------------------------------------------
    // PRODUCT IMPORT
    // -------------------------------------------------------------------------

    /**
     * Import a product from supplier into local products table
     * with margin markup applied
     *
     * $supplier_product = {
     *   external_product_id, title, description, supplier_price,
     *   images[], variants[], category_name, weight
     * }
     */
    public function import_product($supplier_id, $supplier_product, $margin_percent = null)
    {
        $supplier = $this->get_supplier($supplier_id);
        if (empty($supplier)) return false;

        $margin = $margin_percent ?? $supplier->default_margin;
        $selling_price = $this->apply_margin($supplier_product['supplier_price'], $margin);

        // Insert into standard products table with is_dropship flag
        $product_data = [
            'slug'             => str_slug($supplier_product['title']),
            'product_type'     => 'physical',
            'listing_type'     => 'price',
            'sku'              => 'DS-' . strtoupper(uniqid()),
            'price'            => get_price($selling_price, 'database'),
            'currency'         => $supplier->currency,
            'discount_rate'    => 0,
            'vat_rate'         => 0,
            'user_id'          => $supplier->user_id,
            'status'           => 0,         // pending admin approval
            'is_dropship'      => 1,
            'stock'            => 9999,       // unlimited — supplier holds stock
            'multiple_sale'    => 1,
            'is_deleted'       => 0,
            'is_draft'         => 0,
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('products', $product_data);
        $product_id = $this->db->insert_id();

        if (empty($product_id)) return false;

        // Store dropship metadata
        $dropship_data = [
            'product_id'            => $product_id,
            'supplier_id'           => $supplier_id,
            'external_product_id'   => $supplier_product['external_product_id'],
            'supplier_price'        => $supplier_product['supplier_price'],
            'margin_percent'        => $margin,
            'selling_price'         => $selling_price,
            'last_price_checked_at' => date('Y-m-d H:i:s'),
            'created_at'            => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('dropship_products', $dropship_data);

        // Insert product title/description
        $this->db->insert('product_details', [
            'product_id'      => $product_id,
            'lang_id'         => $this->site_lang->id,
            'title'           => $supplier_product['title'],
            'description'     => $supplier_product['description'],
            'seo_title'       => $supplier_product['title'],
            'seo_description' => substr(strip_tags($supplier_product['description']), 0, 160),
            'seo_keywords'    => '',
        ]);

        return $product_id;
    }

    /**
     * Apply margin to supplier cost price
     * Supports: fixed markup, percentage markup, tiered rules
     */
    public function apply_margin($cost_price, $margin_percent)
    {
        return round($cost_price * (1 + ($margin_percent / 100)), 2);
    }

    /**
     * Re-check and update price if supplier price changed
     * Called by cron: Cron_controller::sync_dropship_prices()
     */
    public function sync_product_price($dropship_product_id)
    {
        $dp = $this->db->get_where('dropship_products', ['id' => $dropship_product_id])->row();
        if (empty($dp)) return false;

        $supplier = $this->get_supplier($dp->supplier_id);
        $new_supplier_price = $this->fetch_supplier_price($supplier, $dp->external_product_id);

        if ($new_supplier_price && $new_supplier_price != $dp->supplier_price) {
            $new_selling_price = $this->apply_margin($new_supplier_price, $dp->margin_percent);

            $this->db->where('id', $dropship_product_id);
            $this->db->update('dropship_products', [
                'supplier_price'        => $new_supplier_price,
                'selling_price'         => $new_selling_price,
                'last_price_checked_at' => date('Y-m-d H:i:s'),
            ]);

            $this->db->where('id', $dp->product_id);
            $this->db->update('products', [
                'price' => get_price($new_selling_price, 'database')
            ]);

            return true;
        }
        return false;
    }

    // -------------------------------------------------------------------------
    // ORDER FULFILLMENT
    // -------------------------------------------------------------------------

    /**
     * Auto-fulfill a dropship order item
     * Called from Order_model::add_order_products() after payment confirmed
     *
     * Returns supplier order ID on success, false on failure
     */
    public function auto_fulfill_order_item($order_id, $order_product_id)
    {
        $op = $this->db->get_where('order_products', ['id' => $order_product_id])->row();
        if (empty($op)) return false;

        $dp = $this->db->get_where('dropship_products', ['product_id' => $op->product_id])->row();
        if (empty($dp)) return false;  // not a dropship product

        $supplier = $this->get_supplier($dp->supplier_id);
        if (empty($supplier)) return false;

        // Get shipping address from order
        $shipping = $this->db->get_where('order_shippings', ['order_id' => $order_id])->row();

        // Build fulfillment payload
        $payload = [
            'external_product_id' => $dp->external_product_id,
            'quantity'            => $op->quantity,
            'shipping_address'    => [
                'full_name'  => $shipping->full_name ?? '',
                'address'    => $shipping->address ?? '',
                'city'       => $shipping->city ?? '',
                'state'      => $shipping->state ?? '',
                'zip'        => $shipping->zip_code ?? '',
                'country'    => $shipping->country ?? '',
                'phone'      => $shipping->phone ?? '',
            ],
            'note' => 'Order #' . $order_id . ' via Sellolla',
        ];

        // Call supplier API
        $supplier_order_id = $this->place_supplier_order($supplier, $payload);

        // Save fulfillment record
        $this->db->insert('dropship_fulfillments', [
            'order_id'          => $order_id,
            'order_product_id'  => $order_product_id,
            'supplier_id'       => $supplier->id,
            'supplier_order_id' => $supplier_order_id ?? '',
            'status'            => $supplier_order_id ? 'submitted' : 'failed',
            'payload_sent'      => json_encode($payload),
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        if ($supplier_order_id) {
            // Update order product with fulfillment status
            $this->db->where('id', $order_product_id);
            $this->db->update('order_products', [
                'dropship_fulfillment_status' => 'submitted',
                'dropship_supplier_order_id'  => $supplier_order_id,
            ]);
            return $supplier_order_id;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // TRACKING SYNC
    // -------------------------------------------------------------------------

    /**
     * Pull tracking info from supplier for all submitted fulfillments
     * Called by cron hourly: Cron_controller::sync_dropship_tracking()
     */
    public function sync_tracking()
    {
        $pending = $this->db
            ->where('status', 'submitted')
            ->where('tracking_number IS NULL', null, false)
            ->get('dropship_fulfillments')
            ->result();

        foreach ($pending as $fulfillment) {
            $supplier = $this->get_supplier($fulfillment->supplier_id);
            $tracking = $this->fetch_tracking($supplier, $fulfillment->supplier_order_id);

            if (!empty($tracking['tracking_number'])) {
                $this->db->where('id', $fulfillment->id);
                $this->db->update('dropship_fulfillments', [
                    'tracking_number'  => $tracking['tracking_number'],
                    'tracking_carrier' => $tracking['carrier'] ?? '',
                    'status'           => 'shipped',
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);

                // Update the main order product row
                $this->db->where('id', $fulfillment->order_product_id);
                $this->db->update('order_products', [
                    'dropship_fulfillment_status' => 'shipped',
                    'dropship_tracking_number'    => $tracking['tracking_number'],
                    'dropship_tracking_carrier'   => $tracking['carrier'] ?? '',
                ]);

                // Send tracking email to buyer
                $this->load->model('email_model');
                $order = $this->db->get_where('orders', ['id' => $fulfillment->order_id])->row();
                if (!empty($order) && !empty($order->buyer_id)) {
                    $buyer = get_user($order->buyer_id);
                    if (!empty($buyer)) {
                        $this->email_model->send_dropship_tracking_email(
                            $buyer->email,
                            $order->order_number,
                            $tracking['tracking_number'],
                            $tracking['carrier'] ?? ''
                        );
                    }
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // SUPPLIER API ADAPTERS
    // -------------------------------------------------------------------------

    /**
     * Fetch current supplier price for a product
     * Extend this per supplier type
     */
    private function fetch_supplier_price($supplier, $external_product_id)
    {
        switch ($supplier->api_type) {
            case 'aliexpress':
                return $this->aliexpress_get_price($supplier, $external_product_id);
            case 'cj':
                return $this->cj_get_price($supplier, $external_product_id);
            case 'woocommerce':
                return $this->woo_get_price($supplier, $external_product_id);
            default:
                return null;
        }
    }

    /**
     * Place order at supplier
     */
    private function place_supplier_order($supplier, $payload)
    {
        switch ($supplier->api_type) {
            case 'aliexpress':
                return $this->aliexpress_place_order($supplier, $payload);
            case 'cj':
                return $this->cj_place_order($supplier, $payload);
            case 'woocommerce':
                return $this->woo_place_order($supplier, $payload);
            default:
                return null;
        }
    }

    /**
     * Fetch tracking info from supplier
     */
    private function fetch_tracking($supplier, $supplier_order_id)
    {
        switch ($supplier->api_type) {
            case 'aliexpress':
                return $this->aliexpress_get_tracking($supplier, $supplier_order_id);
            case 'cj':
                return $this->cj_get_tracking($supplier, $supplier_order_id);
            case 'woocommerce':
                return $this->woo_get_tracking($supplier, $supplier_order_id);
            default:
                return [];
        }
    }

    // -------------------------------------------------------------------------
    // ALIEXPRESS ADAPTER (via DSers API or AliExpress Open Platform)
    // -------------------------------------------------------------------------

    private function aliexpress_get_price($supplier, $external_product_id)
    {
        $url = 'https://api.dsers.com/api/v1/products/' . $external_product_id;
        $response = $this->http_get($url, ['Authorization' => 'Bearer ' . $supplier->api_key]);
        if (!empty($response['data']['price'])) {
            return (float)$response['data']['price'];
        }
        return null;
    }

    private function aliexpress_place_order($supplier, $payload)
    {
        $url = 'https://api.dsers.com/api/v1/orders';
        $response = $this->http_post($url, $payload, ['Authorization' => 'Bearer ' . $supplier->api_key]);
        return $response['data']['order_id'] ?? null;
    }

    private function aliexpress_get_tracking($supplier, $supplier_order_id)
    {
        $url = 'https://api.dsers.com/api/v1/orders/' . $supplier_order_id . '/tracking';
        $response = $this->http_get($url, ['Authorization' => 'Bearer ' . $supplier->api_key]);
        return [
            'tracking_number' => $response['data']['tracking_number'] ?? null,
            'carrier'         => $response['data']['carrier'] ?? null,
        ];
    }

    // -------------------------------------------------------------------------
    // CJ DROPSHIPPING ADAPTER
    // -------------------------------------------------------------------------

    private function cj_get_price($supplier, $external_product_id)
    {
        $url = 'https://developers.cjdropshipping.com/api2.0/v1/product/query?pid=' . $external_product_id;
        $response = $this->http_get($url, ['CJ-Access-Token' => $supplier->api_key]);
        return $response['data']['sellPrice'] ?? null;
    }

    private function cj_place_order($supplier, $payload)
    {
        $url = 'https://developers.cjdropshipping.com/api2.0/v1/shopping/order/createOrderV2';
        $cj_payload = [
            'orderNumber'   => 'SELLOLLA-' . time(),
            'products'      => [[
                'vid'      => $payload['external_product_id'],
                'quantity' => $payload['quantity'],
            ]],
            'consignee' => [
                'name'        => $payload['shipping_address']['full_name'],
                'address'     => $payload['shipping_address']['address'],
                'city'        => $payload['shipping_address']['city'],
                'province'    => $payload['shipping_address']['state'],
                'zip'         => $payload['shipping_address']['zip'],
                'countryCode' => $payload['shipping_address']['country'],
                'phone'       => $payload['shipping_address']['phone'],
            ],
        ];
        $response = $this->http_post($url, $cj_payload, ['CJ-Access-Token' => $supplier->api_key]);
        return $response['data']['orderId'] ?? null;
    }

    private function cj_get_tracking($supplier, $supplier_order_id)
    {
        $url = 'https://developers.cjdropshipping.com/api2.0/v1/logistic/tracking?orderId=' . $supplier_order_id;
        $response = $this->http_get($url, ['CJ-Access-Token' => $supplier->api_key]);
        return [
            'tracking_number' => $response['data']['trackNumber'] ?? null,
            'carrier'         => $response['data']['shippingNameEn'] ?? null,
        ];
    }

    // -------------------------------------------------------------------------
    // WOOCOMMERCE SUPPLIER ADAPTER
    // -------------------------------------------------------------------------

    private function woo_get_price($supplier, $external_product_id)
    {
        $url = rtrim($supplier->api_url, '/') . '/wp-json/wc/v3/products/' . $external_product_id;
        $response = $this->http_get($url, [], $supplier->api_key, $supplier->api_secret);
        return isset($response['price']) ? (float)$response['price'] : null;
    }

    private function woo_place_order($supplier, $payload)
    {
        $url = rtrim($supplier->api_url, '/') . '/wp-json/wc/v3/orders';
        $woo_payload = [
            'payment_method' => 'bacs',
            'line_items'     => [[
                'product_id' => $payload['external_product_id'],
                'quantity'   => $payload['quantity'],
            ]],
            'shipping' => [
                'first_name' => $payload['shipping_address']['full_name'],
                'address_1'  => $payload['shipping_address']['address'],
                'city'       => $payload['shipping_address']['city'],
                'state'      => $payload['shipping_address']['state'],
                'postcode'   => $payload['shipping_address']['zip'],
                'country'    => $payload['shipping_address']['country'],
            ],
        ];
        $response = $this->http_post($url, $woo_payload, [], $supplier->api_key, $supplier->api_secret);
        return $response['id'] ?? null;
    }

    private function woo_get_tracking($supplier, $supplier_order_id)
    {
        $url = rtrim($supplier->api_url, '/') . '/wp-json/wc/v3/orders/' . $supplier_order_id;
        $response = $this->http_get($url, [], $supplier->api_key, $supplier->api_secret);
        // WooCommerce tracking typically via shipment-tracking plugin meta
        $tracking_number = null;
        $carrier = null;
        if (!empty($response['meta_data'])) {
            foreach ($response['meta_data'] as $meta) {
                if ($meta['key'] === '_tracking_number') $tracking_number = $meta['value'];
                if ($meta['key'] === '_tracking_provider') $carrier = $meta['value'];
            }
        }
        return ['tracking_number' => $tracking_number, 'carrier' => $carrier];
    }

    // -------------------------------------------------------------------------
    // HTTP HELPERS
    // -------------------------------------------------------------------------

    private function http_get($url, $headers = [], $basic_user = null, $basic_pass = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $h = ['Content-Type: application/json'];
        foreach ($headers as $k => $v) {
            $h[] = "$k: $v";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
        if ($basic_user) {
            curl_setopt($ch, CURLOPT_USERPWD, "$basic_user:$basic_pass");
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true) ?? [];
    }

    private function http_post($url, $payload, $headers = [], $basic_user = null, $basic_pass = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $h = ['Content-Type: application/json'];
        foreach ($headers as $k => $v) {
            $h[] = "$k: $v";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
        if ($basic_user) {
            curl_setopt($ch, CURLOPT_USERPWD, "$basic_user:$basic_pass");
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true) ?? [];
    }

    // -------------------------------------------------------------------------
    // MARGIN RULES
    // -------------------------------------------------------------------------

    /**
     * Tiered margin rules — e.g. high-value items get lower margin
     * Stored in dropship_margin_rules table
     */
    public function get_applicable_margin($supplier_id, $cost_price)
    {
        // Check tiered rules first (ordered by min_price desc so most specific wins)
        $rule = $this->db
            ->where('supplier_id', $supplier_id)
            ->where('min_price <=', $cost_price)
            ->where('status', 1)
            ->order_by('min_price', 'DESC')
            ->limit(1)
            ->get('dropship_margin_rules')
            ->row();

        if ($rule) return $rule->margin_percent;

        // Fall back to supplier default
        $supplier = $this->get_supplier($supplier_id);
        return $supplier ? $supplier->default_margin : 30;
    }

    public function save_margin_rule($data)
    {
        return $this->db->insert('dropship_margin_rules', [
            'supplier_id'    => $data['supplier_id'],
            'label'          => $data['label'],
            'min_price'      => $data['min_price'],
            'margin_percent' => $data['margin_percent'],
            'status'         => 1,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    // -------------------------------------------------------------------------
    // STATS
    // -------------------------------------------------------------------------

    public function get_stats($user_id)
    {
        $supplier_ids = array_column($this->get_suppliers($user_id), 'id');
        if (empty($supplier_ids)) return [];

        return [
            'total_products' => $this->db->where_in('supplier_id', $supplier_ids)->count_all_results('dropship_products'),
            'total_orders'   => $this->db->where_in('supplier_id', $supplier_ids)->count_all_results('dropship_fulfillments'),
            'shipped'        => $this->db->where('status', 'shipped')->where_in('supplier_id', $supplier_ids)->count_all_results('dropship_fulfillments'),
            'pending'        => $this->db->where('status', 'submitted')->where_in('supplier_id', $supplier_ids)->count_all_results('dropship_fulfillments'),
            'failed'         => $this->db->where('status', 'failed')->where_in('supplier_id', $supplier_ids)->count_all_results('dropship_fulfillments'),
        ];
    }
}
