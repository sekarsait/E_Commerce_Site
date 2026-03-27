<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
/* ====================================================
   SELLOLLA — SINGLE-ROW HEADER SYSTEM
   Self-contained. Replaces ALL previous header bars.
   ==================================================== */

/* ── 1. Announcement ticker ────────────────────────── */
#sl-ann {
  background: #0A1628;
  height: 28px;
  overflow: hidden;
  display: flex;
  align-items: center;
}
#sl-ann .sl-track {
  display: inline-flex;
  align-items: center;
  gap: 40px;
  white-space: nowrap;
  animation: sl-ticker 32s linear infinite;
}
#sl-ann .sl-track:hover { animation-play-state: paused; }
#sl-ann .sl-track span {
  font-family: 'DM Sans','Segoe UI',sans-serif;
  font-size: 11px;
  font-weight: 500;
  color: rgba(255,255,255,.8);
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
#sl-ann .sl-track span b { color: #FCD34D; font-weight: 700; }
#sl-ann .sl-dot { color: rgba(255,255,255,.2); font-size: 8px; }
@keyframes sl-ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

/* ── 2. Single-row main header ─────────────────────── */
#sl-header {
  background: #fff;
  border-bottom: 1px solid #E5E7EB;
  position: sticky;
  top: 0;
  z-index: 999;
  box-shadow: 0 1px 6px rgba(0,0,0,.06);
}
#sl-header .sl-row {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 16px;
  height: 56px;
  display: flex;
  align-items: center;
  gap: 10px;
}

/* Logo */
#sl-header .sl-logo {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  text-decoration: none;
  margin-right: 4px;
}
#sl-header .sl-logo img {
  height: 36px;
  width: auto;
  display: block;
}

/* Location pill */
#sl-header .sl-loc {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  cursor: pointer;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1.5px solid transparent;
  transition: border-color .15s;
  text-decoration: none;
  min-width: 90px;
  max-width: 130px;
}
#sl-header .sl-loc:hover { border-color: #E5E7EB; }
#sl-header .sl-loc .sl-loc-label {
  font-size: 10px;
  font-weight: 500;
  color: #9CA3AF;
  line-height: 1;
  display: flex;
  align-items: center;
  gap: 3px;
}
#sl-header .sl-loc .sl-loc-label svg {
  width: 10px; height: 10px;
  fill: #9CA3AF;
  flex-shrink: 0;
}
#sl-header .sl-loc .sl-loc-val {
  font-size: 12px;
  font-weight: 700;
  color: #0A1628;
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Search bar — grows to fill available space */
#sl-header .sl-search {
  flex: 1;
  min-width: 0;
  display: flex;
  align-items: center;
  border: 1.5px solid #E5E7EB;
  border-radius: 8px;
  background: #F9FAFB;
  overflow: hidden;
  transition: all .18s;
  height: 38px;
}
#sl-header .sl-search:focus-within {
  border-color: #F97316;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(249,115,22,.1);
}
/* Category dropdown inside search */
#sl-header .sl-search .sl-cat-drop {
  flex-shrink: 0;
  height: 100%;
  padding: 0 10px;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  border: none;
  border-right: 1px solid #E5E7EB;
  background: #F3F4F6;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  white-space: nowrap;
}
#sl-header .sl-search .sl-cat-drop:focus { outline: none; }
#sl-header .sl-search input {
  flex: 1;
  height: 100%;
  border: none;
  background: transparent;
  padding: 0 12px;
  font-size: 13.5px;
  color: #111827;
  min-width: 0;
}
#sl-header .sl-search input::placeholder { color: #9CA3AF; }
#sl-header .sl-search input:focus { outline: none; }
#sl-header .sl-search .sl-search-btn {
  flex-shrink: 0;
  height: 100%;
  width: 44px;
  background: #F97316;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 15px;
  transition: background .15s;
}
#sl-header .sl-search .sl-search-btn:hover { background: #EA6C0A; }
#sl-header .sl-search-results-ajax { position: absolute; }

/* Right nav items */
#sl-header .sl-right {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 2px;
  margin-left: 4px;
}
#sl-header .sl-right .sl-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4px 9px;
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
  border: 1.5px solid transparent;
  transition: border-color .15s;
  min-width: 52px;
  text-align: center;
  position: relative;
}
#sl-header .sl-right .sl-nav-item:hover { border-color: #E5E7EB; }
#sl-header .sl-right .sl-nav-item .sl-ni-label {
  font-size: 10px;
  color: #9CA3AF;
  font-weight: 500;
  line-height: 1;
  white-space: nowrap;
}
#sl-header .sl-right .sl-nav-item .sl-ni-val {
  font-size: 12px;
  font-weight: 700;
  color: #0A1628;
  line-height: 1.3;
  white-space: nowrap;
}
#sl-header .sl-right .sl-nav-item .sl-ni-icon {
  font-size: 18px;
  color: #0A1628;
  line-height: 1;
}
/* Cart count badge */
#sl-header .sl-cart-count {
  position: absolute;
  top: 2px;
  right: 4px;
  background: #F97316;
  color: #fff;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  font-size: 9px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
/* Sell now button */
#sl-header .sl-right .sl-sell-btn {
  background: #0A1628;
  color: #fff !important;
  border-radius: 6px;
  padding: 7px 14px;
  font-size: 12.5px;
  font-weight: 700;
  text-decoration: none;
  transition: background .15s;
  white-space: nowrap;
  flex-direction: row;
  min-width: auto;
}
#sl-header .sl-right .sl-sell-btn:hover { background: #F97316; border-color: transparent; }

/* Dropdown menus */
#sl-header .sl-right .sl-nav-item .sl-dropdown {
  display: none;
  position: absolute;
  top: calc(100% + 4px);
  right: 0;
  min-width: 180px;
  background: #fff;
  border: 1.5px solid #E5E7EB;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,.1);
  z-index: 1000;
  overflow: hidden;
  text-align: left;
}
#sl-header .sl-right .sl-nav-item:hover .sl-dropdown { display: block; }
#sl-header .sl-right .sl-nav-item .sl-dropdown a,
#sl-header .sl-right .sl-nav-item .sl-dropdown button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 14px;
  font-size: 13px;
  color: #374151;
  text-decoration: none;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: background .12s;
}
#sl-header .sl-right .sl-nav-item .sl-dropdown a:hover,
#sl-header .sl-right .sl-nav-item .sl-dropdown button:hover {
  background: #FFF7ED;
  color: #F97316;
}
#sl-header .sl-right .sl-nav-item .sl-dropdown .sl-dd-sep {
  height: 1px;
  background: #F3F4F6;
  margin: 2px 0;
}

/* Language/currency dropdown */
#sl-header .sl-right .sl-lang .sl-dropdown { min-width: 140px; }
.sl-flag { height: 12px; width: auto; margin-right: 2px; }

/* Unread messages badge */
#sl-header .sl-msg-badge {
  background: #DC2626;
  color: #fff;
  border-radius: 50%;
  width: 15px;
  height: 15px;
  font-size: 9px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-left: 2px;
}

/* ── 3. Trust strip — single thin line ─────────────── */
#sl-trust {
  background: #fff;
  border-bottom: 1px solid #E5E7EB;
  height: 32px;
  overflow: hidden;
}
#sl-trust .sl-ti {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 16px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
}
#sl-trust .sl-tp {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 0 20px;
  font-size: 11px;
  font-weight: 600;
  color: #374151;
  white-space: nowrap;
  border-right: 1px solid #E5E7EB;
  height: 32px;
  font-family: 'DM Sans','Segoe UI',sans-serif;
}
#sl-trust .sl-tp:last-child { border-right: none; }
#sl-trust .sl-tp .sl-ico { font-size: 12px; }
#sl-trust .sl-tp .sl-sub { color: #9CA3AF; font-size: 10px; font-weight: 400; }

/* ── Mobile ────────────────────────────────────────── */
@media (max-width: 991px) {
  #sl-header .sl-loc { display: none; }
  #sl-header .sl-row { gap: 8px; }
}
@media (max-width: 767px) {
  /* On mobile the main header is hidden — Modesy uses mobile-nav-container */
  /* We keep it visible but very compact */
  #sl-header .sl-row { height: 50px; padding: 0 10px; gap: 6px; }
  #sl-header .sl-logo img { height: 28px; }
  #sl-header .sl-right .sl-nav-item:not(.sl-cart-item):not(.sl-account-item):not(.sl-sell-item) {
    display: none;
  }
  #sl-header .sl-right .sl-sell-btn { display: none; }
  #sl-ann { height: 24px; }
  #sl-ann .sl-track span { font-size: 10px; }
  #sl-trust {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  #sl-trust::-webkit-scrollbar { display: none; }
  #sl-trust .sl-ti {
    justify-content: flex-start;
    min-width: max-content;
  }
  #sl-trust .sl-tp { padding: 0 12px; }
  #sl-trust .sl-tp .sl-sub { display: none; }
}

/* ── Location modal fix ─────────────────────────────── */
.input-group-location {
  position: relative;
  display: flex;
  align-items: center;
  border: 1.5px solid #E5E7EB;
  border-radius: 8px;
  overflow: hidden;
  padding: 0 10px;
  background: #F9FAFB;
}
.input-group-location .icon-map-marker {
  color: #9CA3AF;
  font-size: 14px;
  flex-shrink: 0;
  margin-right: 8px;
  position: static !important; /* prevents overlap */
}
.input-group-location .form-control {
  border: none !important;
  background: transparent !important;
  box-shadow: none !important;
  padding: 10px 0 !important;
  font-size: 14px !important;
}
.input-group-location:focus-within {
  border-color: #F97316;
  box-shadow: 0 0 0 3px rgba(249,115,22,.1);
}
.location-modal-description {
  font-size: 13.5px;
  color: #6B7280;
  margin-bottom: 16px;
  line-height: 1.6;
}

/* ── Override old Modesy top-bar if still rendering ─── */
.top-bar, .sellolla-announce, .sellolla-trust-strip,
#sl-announce, #sl-topbar {
  display: none !important;
}
</style>

<!-- ════════════════════════════════════════════════════
     ANNOUNCEMENT TICKER
     ════════════════════════════════════════════════ -->
<div id="sl-ann">
  <div class="sl-track">
    <span>🚚 Free Shipping on orders above <b>₹499</b></span>
    <span class="sl-dot">●</span>
    <span>🔒 <b>100% Secure</b> Payments — UPI, Cards, NetBanking</span>
    <span class="sl-dot">●</span>
    <span>↩ Easy <b>7-Day</b> Returns &amp; Exchanges</span>
    <span class="sl-dot">●</span>
    <span>✅ <b>Verified Sellers</b> — Shop with Confidence</span>
    <span class="sl-dot">●</span>
    <span>📦 <b>Fast Delivery</b> across India</span>
    <span class="sl-dot">●</span>
    <!-- duplicate for seamless loop -->
    <span>🚚 Free Shipping on orders above <b>₹499</b></span>
    <span class="sl-dot">●</span>
    <span>🔒 <b>100% Secure</b> Payments — UPI, Cards, NetBanking</span>
    <span class="sl-dot">●</span>
    <span>↩ Easy <b>7-Day</b> Returns &amp; Exchanges</span>
    <span class="sl-dot">●</span>
    <span>✅ <b>Verified Sellers</b> — Shop with Confidence</span>
    <span class="sl-dot">●</span>
    <span>📦 <b>Fast Delivery</b> across India</span>
  </div>
</div>

<!-- ════════════════════════════════════════════════════
     SINGLE-ROW STICKY MAIN HEADER
     Logo | Location | Search | Language | Account | Cart | Sell
     ════════════════════════════════════════════════ -->
<div id="sl-header">
  <div class="sl-row">

    <!-- LOGO -->
    <a href="<?= lang_base_url(); ?>" class="sl-logo">
      <img src="<?= get_logo($this->general_settings); ?>"
           alt="<?= xss_clean($this->general_settings->application_name); ?>">
    </a>

    <!-- LOCATION (hidden on small screens) -->
    <?php if ($this->general_settings->location_search_header == 1 && item_count($this->countries) > 0): ?>
    <a href="javascript:void(0)" data-toggle="modal" data-target="#locationModal"
       class="sl-loc btn-modal-location" title="Select Location">
      <span class="sl-loc-label">
        <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
        Deliver to
      </span>
      <span class="sl-loc-val">
        <?= !empty($this->default_location_input) ? html_escape($this->default_location_input) : 'Select Location'; ?>
      </span>
    </a>
    <?php endif; ?>

    <!-- SEARCH BAR -->
    <div class="sl-search" style="position:relative;">
      <?php echo form_open(generate_url('search'), ['id' => 'form_validate_search', 'class' => 'form_search_main', 'method' => 'get', 'style' => 'display:flex;align-items:center;width:100%;height:100%;']); ?>

      <?php if ($this->general_settings->multi_vendor_system == 1): ?>
      <div class="sl-cat-drop dropdown">
        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" style="background:none;border:none;font-size:12px;font-weight:600;color:#374151;padding:0 10px;border-right:1px solid #E5E7EB;height:100%;white-space:nowrap;">
          <?php echo isset($search_type) ? trans("member") : trans("product"); ?>
          <span style="font-size:10px;margin-left:2px;">▾</span>
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" data-value="product" href="javascript:void(0)"><?= trans("product"); ?></a>
          <a class="dropdown-item" data-value="member" href="javascript:void(0)"><?= trans("member"); ?></a>
        </div>
        <input type="hidden" class="search_type_input" name="search_type" value="<?= isset($search_type) ? 'member' : 'product'; ?>">
      </div>
      <input type="text" name="search" maxlength="300" pattern=".*\S+.*"
             id="input_search" class="form-control input-search"
             value="<?= !empty($filter_search) ? $filter_search : ''; ?>"
             placeholder="<?= trans("search_exp"); ?>" required autocomplete="off"
             style="flex:1;border:none;background:transparent;padding:0 12px;font-size:13.5px;color:#111827;height:100%;">
      <?php else: ?>
      <input type="text" name="search" maxlength="300" pattern=".*\S+.*"
             id="input_search" class="form-control input-search"
             value="<?= !empty($filter_search) ? $filter_search : ''; ?>"
             placeholder="<?= trans("search_products"); ?>" required autocomplete="off"
             style="flex:1;border:none;background:transparent;padding:0 12px;font-size:13.5px;color:#111827;height:100%;">
      <input type="hidden" class="search_type_input" name="search_type" value="product">
      <?php endif; ?>

      <button class="btn btn-default btn-search sl-search-btn" type="submit">
        <i class="icon-search"></i>
      </button>
      <div id="response_search_results" class="search-results-ajax"></div>
      <?php echo form_close(); ?>
    </div>

    <!-- RIGHT NAV -->
    <div class="sl-right">

      <!-- Language -->
      <?php if ($this->general_settings->multilingual_system == 1 && count($this->languages) > 1): ?>
      <div class="sl-nav-item sl-lang">
        <span class="sl-ni-label">Language</span>
        <span class="sl-ni-val">
          <img src="<?= base_url($this->selected_lang->flag_path); ?>" class="sl-flag">
          <?= html_escape($this->selected_lang->name); ?> ▾
        </span>
        <div class="sl-dropdown">
          <?php foreach ($this->languages as $language): ?>
          <a href="<?= convert_url_by_language($language); ?>"
             class="<?= ($language->id == $this->selected_lang->id) ? 'selected' : ''; ?>">
            <img src="<?= base_url($language->flag_path); ?>" class="sl-flag">
            <?= $language->name; ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Currency -->
      <?php if ($this->payment_settings->currency_converter == 1 && !empty($this->currencies)): ?>
      <div class="sl-nav-item sl-curr">
        <span class="sl-ni-label">Currency</span>
        <span class="sl-ni-val"><?= $this->selected_currency->code; ?> ▾</span>
        <div class="sl-dropdown">
          <?php echo form_open('set-selected-currency-post'); ?>
          <?php foreach ($this->currencies as $currency):
            if ($currency->status == 1): ?>
            <button type="submit" name="currency" value="<?= $currency->code; ?>">
              <?= $currency->code; ?> (<?= $currency->symbol; ?>)
            </button>
          <?php endif; endforeach; ?>
          <?php echo form_close(); ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Account -->
      <?php if ($this->auth_check): ?>
      <div class="sl-nav-item sl-account-item">
        <span class="sl-ni-label">Hello, <?= character_limiter(get_shop_name($this->auth_user), 10, ''); ?></span>
        <span class="sl-ni-val">Account ▾</span>
        <div class="sl-dropdown">
          <?php if ($this->auth_user->role == "admin"): ?>
          <a href="<?= admin_url(); ?>"><i class="icon-admin"></i> <?= trans("admin_panel"); ?></a>
          <?php endif; ?>
          <?php if (is_user_vendor()): ?>
          <a href="<?= dashboard_url(); ?>"><i class="icon-dashboard"></i> <?= trans("dashboard"); ?></a>
          <?php endif; ?>
          <a href="<?= generate_profile_url($this->auth_user->slug); ?>"><i class="icon-user"></i> <?= trans("profile"); ?></a>
          <?php if ($this->is_sale_active): ?>
          <a href="<?= generate_url("orders"); ?>"><i class="icon-shopping-basket"></i> <?= trans("orders"); ?></a>
          <?php endif; ?>
          <a href="<?= generate_url("messages"); ?>">
            <i class="icon-mail"></i> <?= trans("messages"); ?>
            <?php if ($unread_message_count > 0): ?>
            <span class="sl-msg-badge"><?= $unread_message_count; ?></span>
            <?php endif; ?>
          </a>
          <div class="sl-dd-sep"></div>
          <a href="<?= generate_url("settings", "update_profile"); ?>"><i class="icon-settings"></i> <?= trans("settings"); ?></a>
          <a href="<?= base_url(); ?>logout" class="logout"><i class="icon-logout"></i> <?= trans("logout"); ?></a>
        </div>
      </div>
      <?php else: ?>
      <div class="sl-nav-item sl-account-item" data-toggle="modal" data-target="#loginModal" style="cursor:pointer;">
        <span class="sl-ni-label">Hello, Guest</span>
        <span class="sl-ni-val">Sign In ▾</span>
        <div class="sl-dropdown">
          <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal">
            <i class="icon-user"></i> <?= trans("login"); ?>
          </a>
          <a href="<?= generate_url("register"); ?>">
            <i class="icon-user-plus"></i> <?= trans("register"); ?>
          </a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Wishlist -->
      <a href="<?= $this->auth_check ? generate_url("wishlist") . "/" . $this->auth_user->slug : generate_url("wishlist"); ?>"
         class="sl-nav-item" style="text-decoration:none;">
        <i class="icon-heart-o sl-ni-icon"></i>
        <span class="sl-ni-val" style="font-size:11px;font-weight:600;"><?= trans("wishlist"); ?></span>
      </a>

      <!-- Cart -->
      <?php if ($this->is_sale_active):
        $cart_product_count = get_cart_product_count(); ?>
      <a href="<?= generate_url("cart"); ?>" class="sl-nav-item sl-cart-item" style="text-decoration:none;">
        <i class="icon-cart sl-ni-icon"></i>
        <span class="sl-ni-val" style="font-size:11px;font-weight:600;"><?= trans("cart"); ?></span>
        <?php if ($cart_product_count > 0): ?>
        <span class="sl-cart-count"><?= $cart_product_count; ?></span>
        <?php endif; ?>
      </a>
      <?php endif; ?>

      <!-- Sell Now -->
      <?php if (is_multi_vendor_active()): ?>
        <?php if ($this->auth_check): ?>
        <a href="<?= generate_dash_url("add_product"); ?>" class="sl-nav-item sl-sell-btn sl-sell-item">
          Sell Now
        </a>
        <?php else: ?>
        <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal"
           class="sl-nav-item sl-sell-btn sl-sell-item">
          Sell Now
        </a>
        <?php endif; ?>
      <?php endif; ?>

    </div><!-- /sl-right -->
  </div><!-- /sl-row -->
</div><!-- /sl-header -->

<!-- ════════════════════════════════════════════════════
     TRUST PILL STRIP — thin single line
     ════════════════════════════════════════════════ -->
<div id="sl-trust">
  <div class="sl-ti">
    <div class="sl-tp"><span class="sl-ico">🚚</span> Free Delivery <span class="sl-sub">&nbsp;₹499+</span></div>
    <div class="sl-tp"><span class="sl-ico">↩</span> Easy Returns <span class="sl-sub">&nbsp;7 days</span></div>
    <div class="sl-tp"><span class="sl-ico">🔒</span> Secure Payment <span class="sl-sub">&nbsp;256-bit SSL</span></div>
    <div class="sl-tp"><span class="sl-ico">✅</span> 100% Original <span class="sl-sub">&nbsp;Verified sellers</span></div>
    <div class="sl-tp"><span class="sl-ico">📞</span> 24/7 Support</div>
  </div>
</div>
