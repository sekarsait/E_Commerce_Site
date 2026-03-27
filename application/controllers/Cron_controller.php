<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_controller extends Home_Core_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Update Sitemap
     */
    public function update_sitemap()
    {
        $this->load->model('sitemap_model');
        $this->sitemap_model->update_sitemap();
    }
    
    
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dropshipping Cron Methods
 *
 * ADD THESE METHODS to your existing Cron_controller.php
 *
 * Then set up two cron jobs on your server:
 *
 *   # Auto-fulfill new dropship orders (every 5 minutes)
 *   *\/5 * * * * curl -s "https://shop.sellolla.in/cron/fulfill_dropship_orders" > /dev/null
 *
 *   # Sync tracking numbers from suppliers (every hour)
 *   0 * * * * curl -s "https://shop.sellolla.in/cron/sync_dropship_tracking" > /dev/null
 *
 *   # Sync supplier prices (every 6 hours)
 *   0 *\/6 * * * curl -s "https://shop.sellolla.in/cron/sync_dropship_prices" > /dev/null
 *
 * Also add these routes to application/config/routes.php (outside the language loop):
 *
 *   $route['cron/fulfill_dropship_orders'] = 'cron_controller/fulfill_dropship_orders';
 *   $route['cron/sync_dropship_tracking']  = 'cron_controller/sync_dropship_tracking';
 *   $route['cron/sync_dropship_prices']    = 'cron_controller/sync_dropship_prices';
 *
 * Protect cron routes from public access in .htaccess or by IP check in the method.
 */

// ---------------------------------------------------------------------------
// PASTE THESE METHODS INSIDE YOUR Cron_controller CLASS
// ---------------------------------------------------------------------------

    /**
     * Find all paid orders with dropship items that haven't been submitted to
     * the supplier yet, and trigger auto-fulfillment for each.
     *
     * Called every 5 minutes.
     */
    public function fulfill_dropship_orders()
    {
        $this->_cron_guard(); // IP check — see below

        $this->load->model('dropshipping_model');

        // Find order products that:
        //   - belong to a dropship product
        //   - have not yet been fulfilled
        //   - belong to a paid order
        $unfulfilled = $this->db
            ->select('op.id as order_product_id, op.order_id, op.product_id')
            ->from('order_products op')
            ->join('products p', 'p.id = op.product_id')
            ->join('orders o', 'o.id = op.order_id')
            ->where('p.is_dropship', 1)
            ->where('op.dropship_fulfillment_status IS NULL', null, false)
            ->where_in('o.payment_status', ['payment_received'])
            ->where('o.is_deleted', 0)
            ->get()
            ->result();

        $count = 0;
        foreach ($unfulfilled as $item) {
            $result = $this->dropshipping_model->auto_fulfill_order_item(
                $item->order_id,
                $item->order_product_id
            );
            if ($result) $count++;
        }

        echo json_encode([
            'status'    => 'ok',
            'processed' => count($unfulfilled),
            'fulfilled' => $count,
            'time'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Pull tracking numbers from suppliers for all submitted-but-untracked
     * fulfillments. Sends tracking email to buyer when a number is found.
     *
     * Called every hour.
     */
    public function sync_dropship_tracking()
    {
        $this->_cron_guard();

        $this->load->model('dropshipping_model');
        $this->dropshipping_model->sync_tracking();

        echo json_encode(['status' => 'ok', 'time' => date('Y-m-d H:i:s')]);
    }

    /**
     * Re-check supplier prices and update selling prices based on margin rules.
     * Only processes products not checked in the last 6 hours.
     *
     * Called every 6 hours.
     */
    public function sync_dropship_prices()
    {
        $this->_cron_guard();

        $this->load->model('dropshipping_model');

        $stale = $this->db
            ->where('last_price_checked_at <', date('Y-m-d H:i:s', strtotime('-6 hours')))
            ->or_where('last_price_checked_at IS NULL', null, false)
            ->limit(100) // batch to avoid timeout
            ->get('dropship_products')
            ->result();

        $updated = 0;
        foreach ($stale as $dp) {
            if ($this->dropshipping_model->sync_product_price($dp->id)) {
                $updated++;
            }
        }

        echo json_encode([
            'status'  => 'ok',
            'checked' => count($stale),
            'updated' => $updated,
            'time'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Simple IP guard for cron endpoints.
     * Add your server's IP to $allowed_ips.
     * Or replace with a secret token check if you prefer.
     */
    private function _cron_guard()
    {
        $allowed_ips = [
            '127.0.0.1',
            '::1',
            // Add your server's outbound IP here, e.g. '103.xxx.xxx.xxx'
        ];

        if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
            // Uncomment to enforce:
            // http_response_code(403); exit('Forbidden');
        }
    }

// ---------------------------------------------------------------------------
// END OF METHODS TO PASTE
// ---------------------------------------------------------------------------

}
