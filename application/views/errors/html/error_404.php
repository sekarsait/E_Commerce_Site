<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="wrapper">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-7 col-lg-5 text-center" style="padding: 80px 20px;">

        <!-- 404 illustration -->
        <div style="font-size: 80px; line-height: 1; margin-bottom: 16px; user-select:none;">🔍</div>
        <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:72px;font-weight:900;color:#E5E7EB;letter-spacing:-4px;line-height:1;margin-bottom:8px;">404</h1>
        <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:22px;font-weight:800;color:#0A1628;margin-bottom:12px;">Page Not Found</h2>
        <p style="font-size:14.5px;color:#6B7280;line-height:1.7;margin-bottom:32px;">
          The page you're looking for doesn't exist or may have been moved.
          Let's get you back on track.
        </p>

        <!-- Quick links -->
        <div style="display:flex;flex-direction:column;gap:10px;max-width:320px;margin:0 auto 32px;">
          <a href="<?= lang_base_url(); ?>"
             style="background:#F97316;color:#fff;border-radius:50px;padding:13px 28px;font-size:15px;font-weight:700;text-decoration:none;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s;"
             onmouseover="this.style.background='#EA6C0A'"
             onmouseout="this.style.background='#F97316'">
            🏠 Go to Homepage
          </a>
          <a href="<?= generate_url('products'); ?>"
             style="background:#fff;color:#374151;border:1.5px solid #E5E7EB;border-radius:50px;padding:12px 28px;font-size:14px;font-weight:600;text-decoration:none;transition:all .2s;"
             onmouseover="this.style.borderColor='#F97316';this.style.color='#F97316';"
             onmouseout="this.style.borderColor='#E5E7EB';this.style.color='#374151';">
            🛍️ Browse Products
          </a>
          <a href="<?= lang_base_url(); ?>contact"
             style="background:#fff;color:#374151;border:1.5px solid #E5E7EB;border-radius:50px;padding:12px 28px;font-size:14px;font-weight:600;text-decoration:none;transition:all .2s;"
             onmouseover="this.style.borderColor='#F97316';this.style.color='#F97316';"
             onmouseout="this.style.borderColor='#E5E7EB';this.style.color='#374151';">
            💬 Contact Support
          </a>
        </div>

        <p style="font-size:12px;color:#9CA3AF;">
          Error 404 — If you followed a link that brought you here, please
          <a href="<?= lang_base_url(); ?>contact" style="color:#F97316;text-decoration:none;">let us know</a>.
        </p>

      </div>
    </div>
  </div>
</div>
