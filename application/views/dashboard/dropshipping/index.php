<!-- application/views/dashboard/dropshipping/index.php -->
<!-- Modesy Dropshipping Dashboard — Seller View -->

<div class="container py-4">
  <div class="row mb-4">
    <div class="col-md-8">
      <h2 class="mb-1">Dropshipping</h2>
      <p class="text-muted mb-0">Import products from suppliers and fulfil orders automatically.</p>
    </div>
    <div class="col-md-4 text-end">
      <a href="<?= generate_url('dashboard') ?>/dropshipping/import-products" class="btn btn-primary">
        <i class="bi bi-cloud-download me-1"></i> Import Products
      </a>
      <a href="<?= generate_url('dashboard') ?>/dropshipping/add-supplier" class="btn btn-outline-secondary ms-2">
        Add Supplier
      </a>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div class="fs-2 fw-bold text-primary"><?= (int)($stats['total_products'] ?? 0) ?></div>
        <div class="text-muted small">Dropship Products</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div class="fs-2 fw-bold text-warning"><?= (int)($stats['pending'] ?? 0) ?></div>
        <div class="text-muted small">Pending Fulfillment</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div class="fs-2 fw-bold text-success"><?= (int)($stats['shipped'] ?? 0) ?></div>
        <div class="text-muted small">Shipped Orders</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div class="fs-2 fw-bold text-danger"><?= (int)($stats['failed'] ?? 0) ?></div>
        <div class="text-muted small">Failed Fulfillments</div>
      </div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <a href="<?= generate_url('dashboard') ?>/dropshipping/suppliers" class="card card-body text-decoration-none d-flex flex-row align-items-center gap-3">
        <i class="bi bi-shop fs-4 text-primary"></i>
        <div>
          <div class="fw-medium">Suppliers</div>
          <div class="text-muted small"><?= count($suppliers) ?> connected</div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="<?= generate_url('dashboard') ?>/dropshipping/fulfillments" class="card card-body text-decoration-none d-flex flex-row align-items-center gap-3">
        <i class="bi bi-truck fs-4 text-success"></i>
        <div>
          <div class="fw-medium">Fulfillments</div>
          <div class="text-muted small">Track all orders</div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="<?= generate_url('dashboard') ?>/dropshipping/margin-rules" class="card card-body text-decoration-none d-flex flex-row align-items-center gap-3">
        <i class="bi bi-percent fs-4 text-warning"></i>
        <div>
          <div class="fw-medium">Margin Rules</div>
          <div class="text-muted small">Price markup tiers</div>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="<?= generate_url('dashboard') ?>/products" class="card card-body text-decoration-none d-flex flex-row align-items-center gap-3">
        <i class="bi bi-grid fs-4 text-secondary"></i>
        <div>
          <div class="fw-medium">All Products</div>
          <div class="text-muted small">Manage listings</div>
        </div>
      </a>
    </div>
  </div>

  <!-- Recent Fulfillments -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Recent Fulfillments</strong>
      <a href="<?= generate_url('dashboard') ?>/dropshipping/fulfillments" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
      <?php if (empty($recent_fulfillments)): ?>
        <div class="p-4 text-center text-muted">
          No fulfillments yet. Import products and start selling!
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Order #</th>
                <th>Supplier Order</th>
                <th>Status</th>
                <th>Tracking</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_fulfillments as $f): ?>
                <tr>
                  <td><?= esc($f->order_number) ?></td>
                  <td><?= esc($f->supplier_order_id) ?: '<span class="text-muted">—</span>' ?></td>
                  <td>
                    <?php
                      $badge = [
                        'submitted' => 'bg-warning text-dark',
                        'shipped'   => 'bg-success',
                        'failed'    => 'bg-danger',
                        'cancelled' => 'bg-secondary',
                      ][$f->status] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $badge ?>"><?= ucfirst($f->status) ?></span>
                  </td>
                  <td>
                    <?php if (!empty($f->tracking_number)): ?>
                      <a href="https://track.aftership.com/<?= urlencode($f->tracking_number) ?>" target="_blank" class="text-decoration-none">
                        <?= esc($f->tracking_number) ?>
                        <?php if (!empty($f->tracking_carrier)): ?>
                          <span class="text-muted small">(<?= esc($f->tracking_carrier) ?>)</span>
                        <?php endif ?>
                      </a>
                    <?php else: ?>
                      <span class="text-muted">Pending</span>
                    <?php endif ?>
                  </td>
                  <td class="text-muted small"><?= date('d M Y', strtotime($f->created_at)) ?></td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      <?php endif ?>
    </div>
  </div>
</div>
