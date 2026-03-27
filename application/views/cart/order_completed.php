<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="wrapper">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-7 col-lg-6">

        <div class="order-confirm-card">

          <!-- Success animation -->
          <div class="order-confirm-icon">
            <div class="checkmark-circle">
              <svg viewBox="0 0 52 52" class="checkmark-svg">
                <circle class="checkmark-circle-bg" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-tick" fill="none" d="M14 27 l8 8 l16-16"/>
              </svg>
            </div>
          </div>

          <h1 class="order-confirm-title"><?= trans("msg_order_completed"); ?></h1>
          <p class="order-confirm-subtitle">
            Thank you for your order! We have received your payment and will process it shortly.
          </p>

          <!-- Order number badge -->
          <div class="order-number-badge">
            <span class="on-label"><?= trans("order"); ?> Number</span>
            <span class="on-number">#<?= $order->order_number; ?></span>
          </div>

          <!-- Bank transfer info -->
          <?php if ($order->payment_method == "Bank Transfer"): ?>
          <div class="bank-info-box">
            <div class="bib-icon">🏦</div>
            <div class="bib-text">
              <strong>Complete Your Bank Transfer</strong>
              <p><?= trans("msg_bank_transfer_text_order_completed"); ?></p>
            </div>
          </div>
          <div class="bank-account-container">
            <?= $this->payment_settings->bank_transfer_accounts; ?>
          </div>
          <?php endif; ?>

          <!-- What happens next -->
          <div class="order-next-steps">
            <p class="next-steps-title">What happens next?</p>
            <div class="next-step">
              <div class="ns-dot ns-active">1</div>
              <div class="ns-content">
                <strong>Order Confirmed</strong>
                <span>We've received your order</span>
              </div>
            </div>
            <div class="next-step">
              <div class="ns-dot">2</div>
              <div class="ns-content">
                <strong>Processing</strong>
                <span>Seller is preparing your item</span>
              </div>
            </div>
            <div class="next-step">
              <div class="ns-dot">3</div>
              <div class="ns-content">
                <strong>Shipped</strong>
                <span>You'll get a tracking number by email</span>
              </div>
            </div>
            <div class="next-step">
              <div class="ns-dot">4</div>
              <div class="ns-content">
                <strong>Delivered</strong>
                <span>Enjoy your purchase!</span>
              </div>
            </div>
          </div>

          <!-- CTA buttons -->
          <div class="order-confirm-actions">
            <a href="<?= generate_url('orders'); ?>" class="btn btn-confirm-primary">
              📦 Track Your Order
            </a>
            <a href="<?= lang_base_url(); ?>" class="btn btn-confirm-secondary">
              Continue Shopping
            </a>
          </div>

          <!-- Trust footer -->
          <div class="order-trust-footer">
            <span>🔒 Secured by SSL</span>
            <span>📧 Confirmation email sent</span>
            <span>💬 Need help? <a href="<?= lang_base_url(); ?>contact">Contact us</a></span>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<style>
.order-confirm-card {
  background: #fff;
  border-radius: 20px;
  border: 1.5px solid #E5E7EB;
  padding: 40px 36px;
  text-align: center;
  margin: 40px 0;
  box-shadow: 0 4px 24px rgba(0,0,0,.08);
}
/* SVG checkmark animation */
.order-confirm-icon { margin-bottom: 24px; }
.checkmark-circle {
  width: 80px; height: 80px;
  margin: 0 auto;
}
.checkmark-svg { width: 80px; height: 80px; }
.checkmark-circle-bg {
  stroke: #16A34A; stroke-width: 2;
  stroke-dasharray: 166; stroke-dashoffset: 166;
  animation: stroke 0.6s cubic-bezier(0.65,0,0.45,1) forwards;
}
.checkmark-tick {
  stroke: #16A34A; stroke-width: 2.5;
  stroke-linecap: round; stroke-linejoin: round;
  stroke-dasharray: 48; stroke-dashoffset: 48;
  animation: stroke 0.3s cubic-bezier(0.65,0,0.45,1) 0.6s forwards;
}
@keyframes stroke {
  100% { stroke-dashoffset: 0; }
}
.order-confirm-title {
  font-family: 'Plus Jakarta Sans', sans-serif !important;
  font-size: 26px !important;
  font-weight: 800 !important;
  color: #0A1628 !important;
  margin-bottom: 10px !important;
}
.order-confirm-subtitle {
  font-size: 14px; color: #6B7280; margin-bottom: 24px; line-height: 1.6;
}
.order-number-badge {
  display: inline-flex; flex-direction: column; align-items: center;
  background: #F0FDF4; border: 1.5px solid #BBF7D0;
  border-radius: 12px; padding: 12px 32px; margin-bottom: 24px;
}
.on-label { font-size: 11px; color: #16A34A; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
.on-number { font-size: 22px; font-weight: 800; color: #0A1628; font-family: 'Plus Jakarta Sans', sans-serif; margin-top: 2px; }
.bank-info-box {
  display: flex; align-items: flex-start; gap: 12px;
  background: #FFF7ED; border: 1.5px solid #FED7AA;
  border-radius: 10px; padding: 16px; margin-bottom: 16px; text-align: left;
}
.bib-icon { font-size: 22px; }
.bib-text strong { display: block; font-size: 14px; font-weight: 700; color: #0A1628; margin-bottom: 4px; }
.bib-text p { font-size: 13px; color: #6B7280; margin: 0; }
.order-next-steps {
  background: #F9FAFB; border-radius: 12px; padding: 20px;
  margin-bottom: 24px; text-align: left;
}
.next-steps-title {
  font-size: 12px; font-weight: 700; color: #9CA3AF;
  text-transform: uppercase; letter-spacing: .8px; margin-bottom: 14px;
}
.next-step {
  display: flex; align-items: center; gap: 12px; padding: 8px 0;
  border-bottom: 1px solid #F3F4F6;
}
.next-step:last-child { border-bottom: none; }
.ns-dot {
  width: 28px; height: 28px; border-radius: 50%;
  background: #E5E7EB; color: #9CA3AF;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.ns-dot.ns-active { background: #16A34A; color: #fff; }
.ns-content strong { display: block; font-size: 13.5px; font-weight: 700; color: #111827; }
.ns-content span { font-size: 12px; color: #6B7280; }
.order-confirm-actions {
  display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px;
}
.btn-confirm-primary {
  background: #F97316; color: #fff; border: none;
  border-radius: 50px; padding: 14px 28px;
  font-size: 15px; font-weight: 700;
  font-family: 'Plus Jakarta Sans', sans-serif;
  text-decoration: none; transition: all .2s;
  display: block;
}
.btn-confirm-primary:hover {
  background: #EA6C0A; color: #fff; transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(249,115,22,.3);
}
.btn-confirm-secondary {
  background: #fff; color: #374151;
  border: 1.5px solid #E5E7EB;
  border-radius: 50px; padding: 13px 28px;
  font-size: 14px; font-weight: 600;
  text-decoration: none; transition: all .2s;
  display: block;
}
.btn-confirm-secondary:hover {
  border-color: #F97316; color: #F97316;
}
.order-trust-footer {
  display: flex; justify-content: center; gap: 20px;
  flex-wrap: wrap; font-size: 12px; color: #9CA3AF;
}
.order-trust-footer a { color: #F97316; text-decoration: none; }
@media (max-width: 480px) {
  .order-confirm-card { padding: 28px 20px; }
  .order-trust-footer { gap: 12px; }
}
</style>
