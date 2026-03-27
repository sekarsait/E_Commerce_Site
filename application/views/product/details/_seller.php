<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ── Premium Seller Card ──────────────────────────── -->
<div class="widget-seller-premium">

  <!-- Seller identity -->
  <div class="seller-identity">
    <a href="<?= generate_profile_url($product->user_slug); ?>" class="seller-avatar-link">
      <img src="<?= get_user_avatar($user); ?>"
           alt="<?= get_shop_name($user); ?>"
           class="seller-avatar">
      <?php if (is_user_online($user->last_seen)): ?>
      <span class="online-dot" title="Online now"></span>
      <?php endif; ?>
    </a>
    <div class="seller-meta">
      <a href="<?= generate_profile_url($product->user_slug); ?>" class="seller-name">
        <?= get_shop_name($user); ?>
      </a>
      <span class="seller-online-status <?= is_user_online($user->last_seen) ? 'online' : ''; ?>">
        <i class="icon-circle"></i>
        <?php if (is_user_online($user->last_seen)): ?>
          Active now
        <?php else: ?>
          <?= trans("last_seen"); ?> <?= time_ago($user->last_seen); ?>
        <?php endif; ?>
      </span>
      <?php if (!empty($user->phone_number) && $user->show_phone == 1): ?>
      <span class="seller-phone">
        <i class="icon-phone"></i>
        <a href="javascript:void(0)" id="show_phone_number"><?= trans("show"); ?></a>
        <a href="tel:<?= html_escape($user->phone_number); ?>" id="phone_number" class="display-none"><?= html_escape($user->phone_number); ?></a>
      </span>
      <?php elseif (!empty($user->email) && $user->show_email == 1): ?>
      <span class="seller-phone">
        <i class="icon-envelope"></i><?= html_escape($user->email); ?>
      </span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Seller actions -->
  <div class="seller-actions">
    <?php if ($this->auth_check): ?>
      <?php if ($this->auth_user->id != $user->id): ?>
        <?php echo form_open('follow-unfollow-user-post', ['class' => 'form-inline']); ?>
        <input type="hidden" name="following_id" value="<?= $user->id; ?>">
        <input type="hidden" name="follower_id" value="<?= $this->auth_user->id; ?>">
        <?php if (is_user_follows($user->id, $this->auth_user->id)): ?>
          <button class="btn btn-seller-action btn-unfollow">
            <i class="icon-user-minus"></i> <?= trans("unfollow"); ?>
          </button>
        <?php else: ?>
          <button class="btn btn-seller-action btn-follow">
            <i class="icon-user-plus"></i> <?= trans("follow"); ?>
          </button>
        <?php endif; ?>
        <?php echo form_close(); ?>
      <?php endif; ?>
    <?php else: ?>
      <button class="btn btn-seller-action btn-follow"
              data-toggle="modal" data-target="#loginModal">
        <i class="icon-user-plus"></i> <?= trans("follow"); ?>
      </button>
    <?php endif; ?>
  </div>

  <!-- Trust badges for this seller -->
  <div class="seller-trust-row">
    <div class="seller-trust-item">
      <i class="icon-shield" style="color:#16A34A;"></i>
      <span>Verified Seller</span>
    </div>
    <div class="seller-trust-item">
      <i class="icon-star" style="color:#F59E0B;"></i>
      <span><?= !empty($user->rating) ? round($user->rating, 1) . ' Rating' : 'New Seller'; ?></span>
    </div>
  </div>

  <!-- More products from this seller -->
  <?php if (!empty($user_products)): ?>
  <div class="more-from-seller">
    <p class="more-seller-title">
      More from <strong><?= get_shop_name($user); ?></strong>
    </p>
    <div class="row g-1">
      <?php foreach ($user_products as $item): ?>
      <div class="col-4">
        <a href="<?= generate_product_url($item); ?>" class="more-seller-img-link">
          <img src="<?= get_product_image($item->id, 'image_small'); ?>"
               alt="<?= get_product_title($item); ?>"
               class="img-fluid more-seller-img">
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <a href="<?= generate_profile_url($product->user_slug); ?>" class="view-all-seller-link">
      View all products →
    </a>
  </div>
  <?php endif; ?>

</div>

<!-- ── Product Trust Box (delivery, returns, secure) ── -->
<div class="product-trust-box-premium">
  <div class="ptb-row">
    <div class="ptb-icon" style="color:#16A34A;">🚚</div>
    <div class="ptb-text">
      <strong>Free Delivery</strong>
      <span>On orders above ₹499. Usually 3–7 days.</span>
    </div>
  </div>
  <div class="ptb-row">
    <div class="ptb-icon" style="color:#2563EB;">↩</div>
    <div class="ptb-text">
      <strong>7-Day Easy Returns</strong>
      <span>Not satisfied? Return it, hassle-free.</span>
    </div>
  </div>
  <div class="ptb-row">
    <div class="ptb-icon" style="color:#F59E0B;">🔒</div>
    <div class="ptb-text">
      <strong>Secure Payment</strong>
      <span>UPI, Cards, NetBanking — all 256-bit encrypted.</span>
    </div>
  </div>
  <div class="ptb-row">
    <div class="ptb-icon" style="color:#9333EA;">✅</div>
    <div class="ptb-text">
      <strong>100% Authentic</strong>
      <span>All sellers are verified before listing.</span>
    </div>
  </div>
</div>

<style>
/* ── Seller Widget ──────────────────────────────────── */
.widget-seller-premium {
  background: var(--white, #fff);
  border: 1.5px solid var(--gray-200, #E5E7EB);
  border-radius: 14px;
  padding: 20px;
  margin-bottom: 16px;
}
.seller-identity {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 14px;
  position: relative;
}
.seller-avatar-link { position: relative; flex-shrink: 0; }
.seller-avatar {
  width: 52px; height: 52px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--gray-200, #E5E7EB);
}
.online-dot {
  position: absolute; bottom: 2px; right: 2px;
  width: 11px; height: 11px;
  background: #16A34A;
  border-radius: 50%;
  border: 2px solid #fff;
}
.seller-meta { flex: 1; min-width: 0; }
.seller-name {
  display: block;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14.5px;
  font-weight: 700;
  color: #0A1628;
  text-decoration: none;
  margin-bottom: 3px;
}
.seller-name:hover { color: #F97316; }
.seller-online-status {
  display: flex; align-items: center; gap: 5px;
  font-size: 11.5px; color: #6B7280;
}
.seller-online-status .icon-circle { font-size: 7px; color: #9CA3AF; }
.seller-online-status.online .icon-circle { color: #16A34A; }
.seller-online-status.online { color: #16A34A; }
.seller-phone {
  display: flex; align-items: center; gap: 5px;
  font-size: 12px; color: #6B7280; margin-top: 3px;
}
.btn-seller-action {
  font-size: 12.5px; font-weight: 600;
  border-radius: 50px; padding: 6px 16px;
  border: 1.5px solid #E5E7EB;
  background: #fff; color: #374151;
  transition: all .2s;
}
.btn-seller-action.btn-follow:hover {
  border-color: #F97316; color: #F97316;
}
.btn-seller-action.btn-unfollow:hover {
  border-color: #DC2626; color: #DC2626;
}
.seller-trust-row {
  display: flex; gap: 12px;
  padding: 12px 0;
  border-top: 1px solid #F3F4F6;
  border-bottom: 1px solid #F3F4F6;
  margin: 14px 0;
}
.seller-trust-item {
  display: flex; align-items: center; gap: 5px;
  font-size: 12px; font-weight: 600; color: #374151;
}
.more-seller-title {
  font-size: 13px; color: #6B7280; margin-bottom: 10px;
}
.more-seller-img-link { display: block; }
.more-seller-img {
  width: 100%; aspect-ratio: 1;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #F3F4F6;
  transition: all .2s;
}
.more-seller-img:hover { transform: scale(1.04); }
.view-all-seller-link {
  display: inline-block; margin-top: 10px;
  font-size: 12.5px; font-weight: 600; color: #F97316;
  text-decoration: none;
}
.view-all-seller-link:hover { color: #EA6C0A; }

/* ── Product Trust Box ───────────────────────────────── */
.product-trust-box-premium {
  background: #F9FAFB;
  border: 1.5px solid #E5E7EB;
  border-radius: 12px;
  padding: 4px 0;
  margin-bottom: 16px;
}
.ptb-row {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 16px;
  border-bottom: 1px solid #F3F4F6;
}
.ptb-row:last-child { border-bottom: none; }
.ptb-icon { font-size: 20px; flex-shrink: 0; width: 28px; text-align: center; }
.ptb-text strong {
  display: block; font-size: 13px; font-weight: 700;
  color: #111827; font-family: 'Plus Jakarta Sans', sans-serif;
}
.ptb-text span { font-size: 11.5px; color: #6B7280; }
</style>
