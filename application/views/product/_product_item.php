<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="product-item">

  <!-- ── Image area ──────────────────────────────── -->
  <div class="row-custom<?= (!empty($product->image_second)) ? ' product-multiple-image' : ''; ?>">

    <!-- Hidden wishlist toggle covers image -->
    <a class="item-wishlist-button item-wishlist-enable <?= (is_product_in_wishlist($product) == 1) ? 'item-wishlist' : ''; ?>"
       data-product-id="<?= $product->id; ?>"></a>

    <div class="img-product-container">

      <!-- Product image -->
      <?php if (!empty($is_slider)): ?>
      <a href="<?= generate_product_url($product); ?>">
        <img src="<?= base_url() . IMG_BG_PRODUCT_SMALL; ?>"
             data-lazy="<?= get_product_item_image($product); ?>"
             alt="<?= get_product_title($product); ?>"
             class="img-fluid img-product">
        <?php if (!empty($product->image_second)): ?>
        <img src="<?= base_url() . IMG_BG_PRODUCT_SMALL; ?>"
             data-lazy="<?= get_product_item_image($product, true); ?>"
             alt="<?= get_product_title($product); ?>"
             class="img-fluid img-product img-second">
        <?php endif; ?>
      </a>
      <?php else: ?>
      <a href="<?= generate_product_url($product); ?>">
        <img src="<?= base_url() . IMG_BG_PRODUCT_SMALL; ?>"
             data-src="<?= get_product_item_image($product); ?>"
             alt="<?= get_product_title($product); ?>"
             class="lazyload img-fluid img-product">
        <?php if (!empty($product->image_second)): ?>
        <img src="<?= base_url() . IMG_BG_PRODUCT_SMALL; ?>"
             data-src="<?= get_product_item_image($product, true); ?>"
             alt="<?= get_product_title($product); ?>"
             class="lazyload img-fluid img-product img-second">
        <?php endif; ?>
      </a>
      <?php endif; ?>

      <!-- Quick action buttons (appear on hover) -->
      <div class="product-item-options">
        <!-- Wishlist -->
        <a href="javascript:void(0)"
           class="item-option btn-add-remove-wishlist"
           data-toggle="tooltip" data-placement="left"
           data-product-id="<?= $product->id; ?>"
           data-reload="0"
           title="<?= trans("wishlist"); ?>">
          <?php if (is_product_in_wishlist($product) == 1): ?>
          <i class="icon-heart" style="color:#F97316;"></i>
          <?php else: ?>
          <i class="icon-heart-o"></i>
          <?php endif; ?>
        </a>

        <!-- Add to cart / view options -->
        <?php if (($product->listing_type == "sell_on_site" || $product->listing_type == "bidding") && $product->is_free_product != 1): ?>
          <?php if (!empty($product->has_variation) || $product->listing_type == "bidding"): ?>
          <a href="<?= generate_product_url($product); ?>"
             class="item-option"
             data-toggle="tooltip" data-placement="left"
             data-product-id="<?= $product->id; ?>"
             title="<?= trans("view_options"); ?>">
            <i class="icon-cart"></i>
          </a>
          <?php else: ?>
          <a href="javascript:void(0)"
             class="item-option btn-item-add-to-cart"
             data-toggle="tooltip" data-placement="left"
             data-product-id="<?= $product->id; ?>"
             data-reload="0"
             title="<?= trans("add_to_cart"); ?>">
            <i class="icon-cart"></i>
          </a>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Discount badge -->
      <?php if (!empty($product->discount_rate) && !empty($discount_label)): ?>
      <span class="badge badge-discount">-<?= $product->discount_rate; ?>%</span>
      <?php endif; ?>

    </div><!-- /img-product-container -->

    <!-- Featured badge -->
    <?php if ($product->is_promoted && $this->general_settings->promoted_products == 1 && isset($promoted_badge) && $promoted_badge == true): ?>
    <span class="badge badge-dark badge-promoted">⭐ <?= trans("featured"); ?></span>
    <?php endif; ?>

  </div><!-- /row-custom -->

  <!-- ── Product Info ─────────────────────────────── -->
  <div class="row-custom item-details">

    <!-- Title -->
    <h3 class="product-title">
      <a href="<?= generate_product_url($product); ?>"><?= get_product_title($product); ?></a>
    </h3>

    <!-- Seller name -->
    <p class="product-user text-truncate">
      <a href="<?= generate_profile_url($product->user_slug); ?>">
        <?= get_shop_name_product($product); ?>
      </a>
    </p>

    <!-- Rating + wishlist count -->
    <div class="product-item-rating">
      <?php if ($this->general_settings->reviews == 1): ?>
        <?php $this->load->view('partials/_review_stars', ['review' => $product->rating]); ?>
      <?php endif; ?>
      <span class="item-wishlist">
        <i class="icon-heart-o"></i><?= $product->wishlist_count; ?>
      </span>
    </div>

    <!-- Price -->
    <div class="item-meta">
      <?php $this->load->view('product/_price_product_item', ['product' => $product]); ?>
    </div>

    <!-- Free delivery badge (show when listing type supports it) -->
    <?php if ($product->product_type == 'physical'): ?>
    <div style="margin-top:7px;">
      <span style="font-size:11px;color:#16A34A;font-weight:600;">
        🚚 Free Delivery
      </span>
    </div>
    <?php endif; ?>

  </div><!-- /item-details -->

</div><!-- /product-item -->
