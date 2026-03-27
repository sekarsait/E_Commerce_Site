<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<footer id="footer">
  <div class="container">
    <div class="footer-top">
      <div class="row">

        <!-- Col 1: Brand + Payments -->
        <div class="col-12 col-md-4 col-lg-3 footer-widget mb-4">
          <div class="footer-logo mb-3">
            <a href="<?= lang_base_url(); ?>">
              <img src="<?= get_logo($this->general_settings); ?>"
                   alt="<?= xss_clean($this->general_settings->application_name); ?>">
            </a>
          </div>
          <div class="footer-about mb-4">
            <?= $this->settings->about_footer; ?>
          </div>

          <!-- Payment icons — inline SVG so no file loading needed -->
          <p style="font-size:10.5px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.8px;font-weight:700;margin-bottom:10px;">We Accept</p>
          <div style="display:flex;flex-wrap:wrap;gap:7px;align-items:center;margin-bottom:6px;">

            <!-- Razorpay (from file if exists, else text) -->
            <img src="<?= base_url(); ?>assets/img/payment/razorpay.svg"
                 alt="Razorpay"
                 style="height:18px;width:auto;filter:brightness(0) invert(1);opacity:.75;"
                 onerror="this.outerHTML='<span class=\'pay-badge\'>Razorpay</span>'">

            <!-- Visa inline SVG -->
            <svg height="20" viewBox="0 0 60 38" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:.7">
              <rect width="60" height="38" rx="4" fill="rgba(255,255,255,.1)"/>
              <path d="M22.5 25L25.5 13H28L25 25H22.5ZM35.5 13.3C34.9 13.1 33.9 12.9 32.7 12.9C30.1 12.9 28.3 14.2 28.3 16C28.3 17.4 29.5 18.2 30.5 18.7C31.5 19.2 31.9 19.5 31.9 20C31.9 20.8 31 21.1 30.1 21.1C28.8 21.1 28.1 20.9 27 20.4L26.5 20.2L26 23.2C26.7 23.5 28.1 23.8 29.5 23.8C32.3 23.8 34.1 22.5 34.1 20.5C34.1 19.4 33.4 18.6 31.9 17.9C31 17.5 30.5 17.2 30.5 16.6C30.5 16.1 31 15.5 32.2 15.5C33.2 15.5 33.9 15.7 34.5 15.9L34.8 16L35.5 13.3ZM41 13H39C38.3 13 37.8 13.2 37.5 13.9L33.5 25H36.3C36.3 25 36.8 23.6 36.9 23.3H40.3C40.4 23.7 40.7 25 40.7 25H43.2L41 13ZM37.6 21.2C37.8 20.6 38.9 17.7 38.9 17.7C38.9 17.7 39.2 16.9 39.4 16.4L39.6 17.6C39.6 17.6 40.2 20.3 40.3 21.2H37.6ZM21 13L18.4 21L18.1 19.5C17.6 17.8 16 15.9 14.2 14.9L16.6 25H19.5L23.9 13H21Z" fill="white"/>
              <path d="M15.9 13H11.5L11.4 13.3C14.8 14.1 17.1 16.1 18.1 18.5L17 14C16.8 13.2 16.4 13.1 15.9 13Z" fill="#F9A533"/>
            </svg>

            <!-- Mastercard inline SVG -->
            <svg height="20" viewBox="0 0 38 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:.7">
              <rect width="38" height="24" rx="3" fill="rgba(255,255,255,.05)"/>
              <circle cx="15" cy="12" r="7" fill="#EB001B"/>
              <circle cx="23" cy="12" r="7" fill="#F79E1B"/>
              <path d="M19 7.5C20.7 8.7 21.9 10.2 21.9 12C21.9 13.8 20.7 15.3 19 16.5C17.3 15.3 16.1 13.8 16.1 12C16.1 10.2 17.3 8.7 19 7.5Z" fill="#FF5F00"/>
            </svg>

            <!-- RuPay badge -->
            <span class="pay-badge" style="background:rgba(255,155,0,.15);border-color:rgba(255,155,0,.3);color:rgba(255,200,100,.9);">RuPay</span>

            <!-- UPI badge -->
            <span class="pay-badge" style="background:rgba(100,180,255,.15);border-color:rgba(100,180,255,.3);color:rgba(150,210,255,.9);">UPI</span>

            <!-- NetBanking badge -->
            <span class="pay-badge">NetBanking</span>

            <!-- COD badge -->
            <span class="pay-badge">COD</span>
          </div>
        </div>

        <!-- Col 2: Quick Links -->
        <div class="col-6 col-md-2 col-lg-2 footer-widget mb-4">
          <div class="nav-footer">
            <h4 class="footer-title"><?= trans("footer_quick_links"); ?></h4>
            <ul>
              <li><a href="<?= lang_base_url(); ?>"><?= trans("home"); ?></a></li>
              <?php if (!empty($this->menu_links)):
                foreach ($this->menu_links as $menu_link):
                  if ($menu_link->location == 'quick_links'):
                    $item_link = generate_menu_item_url($menu_link);
                    if (!empty($menu_link->page_default_name)):
                      $item_link = generate_url($menu_link->page_default_name);
                    endif; ?>
                    <li><a href="<?= $item_link; ?>"><?= html_escape($menu_link->title); ?></a></li>
                  <?php endif;
                endforeach;
              endif; ?>
            </ul>
          </div>
        </div>

        <!-- Col 3: Legal — hardcoded compliance links PLUS any admin-added "information" links -->
        <div class="col-6 col-md-2 col-lg-2 footer-widget mb-4">
          <div class="nav-footer">
            <h4 class="footer-title">Legal</h4>
            <ul>
              <!-- Admin-added links (location = information) -->
              <?php if (!empty($this->menu_links)):
                foreach ($this->menu_links as $menu_link):
                  if ($menu_link->location == 'information'):
                    $item_link = generate_menu_item_url($menu_link);
                    if (!empty($menu_link->page_default_name)):
                      $item_link = generate_url($menu_link->page_default_name);
                    endif; ?>
                    <li><a href="<?= $item_link; ?>"><?= html_escape($menu_link->title); ?></a></li>
                  <?php endif;
                endforeach;
              endif; ?>
              <!-- Hardcoded compliance pages — always show regardless of admin panel -->
              <li><a href="<?= lang_base_url(); ?>terms-and-conditions.html">Terms & Conditions</a></li>
              <li><a href="<?= lang_base_url(); ?>privacy-policy.html">Privacy Policy</a></li>
              <li><a href="<?= lang_base_url(); ?>return-policy.html">Return &amp; Refund Policy</a></li>
              <li><a href="<?= lang_base_url(); ?>shipping-policy.html">Shipping Policy</a></li>
              <li><a href="<?= lang_base_url(); ?>grievance-officer.html">Grievance Officer</a></li>
            </ul>
          </div>
        </div>

        <!-- Col 4: Help -->
        <div class="col-6 col-md-2 col-lg-2 footer-widget mb-4">
          <div class="nav-footer">
            <h4 class="footer-title">Help</h4>
            <ul>
              <li><a href="<?= lang_base_url(); ?>contact"><?= trans("contact"); ?></a></li>
              <li><a href="<?= generate_url('start_selling'); ?>">Sell on <?= xss_clean($this->general_settings->application_name); ?></a></li>
              <li><a href="<?= generate_url('orders'); ?>">Track Your Order</a></li>
              <li><a href="<?= lang_base_url(); ?>faq">FAQs</a></li>
              <li><a href="<?= lang_base_url(); ?>seller-guidelines">Seller Guidelines</a></li>
            </ul>
          </div>
        </div>

        <!-- Col 5: Social + Newsletter -->
        <div class="col-12 col-md-4 col-lg-3 footer-widget mb-4">
          <h4 class="footer-title"><?= trans("follow_us"); ?></h4>
          <div class="footer-social-links mb-4">
            <?php $this->load->view('partials/_social_links', ['show_rss' => true]); ?>
          </div>

          <div class="newsletter">
            <h4 class="footer-title"><?= trans("newsletter"); ?></h4>
            <p style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:10px;">Get deals &amp; offers in your inbox.</p>
            <?php echo form_open('add-to-subscribers-post', ['id' => 'form_validate_newsletter']); ?>
            <div class="newsletter-inner">
              <input type="email" class="form-control" name="email"
                     placeholder="Your email address"
                     maxlength="250" required>
              <button class="btn btn-default"><?= trans("subscribe"); ?></button>
            </div>
            <?php echo form_close(); ?>
            <div id="newsletter" class="m-t-5" style="font-size:12px;">
              <?php if ($this->session->flashdata('news_error')):
                echo '<span class="text-danger">' . $this->session->flashdata('news_error') . '</span>';
              endif;
              if ($this->session->flashdata('news_success')):
                echo '<span class="text-success">' . $this->session->flashdata('news_success') . '</span>';
              endif; ?>
            </div>
          </div>
        </div>

      </div>
    </div><!-- /footer-top -->

    <!-- Indian Compliance Strip -->
    <div style="border-top:1px solid rgba(255,255,255,.08);padding:14px 0;text-align:center;">
      <p style="font-size:11.5px;color:rgba(255,255,255,.45);margin:0 0 5px;line-height:1.8;">
        <strong style="color:rgba(255,255,255,.6);">Registered Business:</strong>
        <?= xss_clean($this->general_settings->application_name); ?> — GST Registered &nbsp;|&nbsp;
        CIN: [Your Company Registration Number] &nbsp;|&nbsp;
        <a href="<?= lang_base_url(); ?>grievance-officer.html" style="color:rgba(255,255,255,.55);">Grievance Officer</a>:
        [Name], <a href="mailto:grievance@sellolla.in" style="color:#F97316;">grievance@sellolla.in</a>
      </p>
      <p style="font-size:11px;color:rgba(255,255,255,.3);margin:0;line-height:1.8;">
        In accordance with the
        <a href="https://consumeraffairs.nic.in" target="_blank" style="color:rgba(255,255,255,.45);">Consumer Protection (E-Commerce) Rules 2020</a>,
        Information Technology Act 2000 and RBI payment guidelines.
        All transactions are secured by SSL encryption.
      </p>
    </div>

  </div><!-- /container -->

  <!-- Footer Bottom -->
 <!-- <div class="container-fluid">
    <div class="row">
      <div class="footer-bottom w-100">
        <div class="container d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;padding-top:12px;padding-bottom:12px;">
          <div class="copyright" style="color:rgba(255,255,255,.4);font-size:12px;">
            <?= html_escape($this->settings->copyright); ?>
          </div> -->
<div class="container d-flex justify-content-center">
  <div class="copyright text-center" style="color:rgba(255,255,255,.4);font-size:12px;width:100%;">
    <?= html_escape($this->settings->copyright); ?>
  </div>
</div>
          
          <!-- Bottom payment icons — use inline SVG so no loading issues -->
         <!-- <div style="display:flex;align-items:center;gap:8px;">
            <svg height="18" viewBox="0 0 60 38" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:.5">
              <rect width="60" height="38" rx="4" fill="rgba(255,255,255,.15)"/>
              <path d="M22.5 25L25.5 13H28L25 25H22.5ZM35.5 13.3C34.9 13.1 33.9 12.9 32.7 12.9C30.1 12.9 28.3 14.2 28.3 16C28.3 17.4 29.5 18.2 30.5 18.7C31.5 19.2 31.9 19.5 31.9 20C31.9 20.8 31 21.1 30.1 21.1C28.8 21.1 28.1 20.9 27 20.4L26.5 20.2L26 23.2C26.7 23.5 28.1 23.8 29.5 23.8C32.3 23.8 34.1 22.5 34.1 20.5C34.1 19.4 33.4 18.6 31.9 17.9C31 17.5 30.5 17.2 30.5 16.6C30.5 16.1 31 15.5 32.2 15.5C33.2 15.5 33.9 15.7 34.5 15.9L34.8 16L35.5 13.3ZM21 13L18.4 21L18.1 19.5C17.6 17.8 16 15.9 14.2 14.9L16.6 25H19.5L23.9 13H21Z" fill="rgba(255,255,255,.7)"/>
            </svg>
            <svg height="18" viewBox="0 0 38 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:.5">
              <rect width="38" height="24" rx="3" fill="rgba(255,255,255,.15)"/>
              <circle cx="15" cy="12" r="7" fill="rgba(235,0,27,.6)"/>
              <circle cx="23" cy="12" r="7" fill="rgba(247,158,27,.6)"/>
            </svg>
            <span style="font-size:10px;color:rgba(255,255,255,.4);font-weight:700;letter-spacing:.5px;">UPI</span>
            <span style="font-size:10px;color:rgba(255,255,255,.4);font-weight:700;letter-spacing:.5px;">NetBanking</span>
            <span style="font-size:10px;color:rgba(255,255,255,.4);font-weight:700;letter-spacing:.5px;">COD</span>
          </div>
          -->
        </div>
      </div>
    </div>
  </div>

</footer>

<!-- Cookie warning -->
<?php if (!isset($_COOKIE["modesy_cookies_warning"]) && $this->settings->cookies_warning): ?>
<div class="cookies-warning">
  <div class="text"><?= $this->settings->cookies_warning_text; ?></div>
  <a href="javascript:void(0)" onclick="hide_cookies_warning();" class="icon-cl"><i class="icon-close"></i></a>
</div>
<?php endif; ?>

<a href="javascript:void(0)" class="scrollup"><i class="icon-arrow-up"></i></a>

<script src="<?= base_url(); ?>assets/js/jquery-3.5.1.min.js"></script>
<script src="<?= base_url(); ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url(); ?>assets/js/plugins-1.8.js"></script>
<script src="<?= base_url(); ?>assets/js/script-1.8.min.js"></script>
<?php if (!empty($this->session->userdata('mds_send_email_data'))): ?>
<script>
$(document).ready(function(){
  var data=JSON.parse(<?= json_encode($this->session->userdata("mds_send_email_data"));?>);
  if(data){data[mds_config.csfr_token_name]=$.cookie(mds_config.csfr_cookie_name);data["sys_lang_id"]=mds_config.sys_lang_id;$.ajax({type:"POST",url:"<?= base_url(); ?>mds-send-email-post",data:data});}
});
</script>
<?php endif; $this->session->unset_userdata('mds_send_email_data'); ?>
<?php if (check_cron_time()==true): ?>
<script>$.ajax({type:"POST",url:"<?= base_url(); ?>mds-run-internal-cron"});</script>
<?php endif; ?>
<script>$('<input>').attr({type:'hidden',name:'sys_lang_id',value:'<?= $this->selected_lang->id; ?>'}).appendTo('form[method="post"]');</script>
<script>
<?php if (!empty($index_categories)):foreach($index_categories as $category):?>
if($('#category_products_slider_<?= $category->id; ?>').length!=0){
  $('#category_products_slider_<?= $category->id; ?>').slick({autoplay:false,infinite:true,speed:200,swipeToSlide:true,rtl:mds_config.rtl,cssEase:'linear',prevArrow:$('#category-products-slider-nav-<?= $category->id; ?> .prev'),nextArrow:$('#category-products-slider-nav-<?= $category->id; ?> .next'),slidesToShow:5,slidesToScroll:1,responsive:[{breakpoint:992,settings:{slidesToShow:4}},{breakpoint:768,settings:{slidesToShow:3}},{breakpoint:576,settings:{slidesToShow:2}}]});
}
<?php endforeach; endif;?>
<?php if($this->general_settings->pwa_status==1):?>
if('serviceWorker' in navigator){window.addEventListener('load',function(){navigator.serviceWorker.register('<?= base_url();?>pwa-sw.js');});}
<?php endif;?>
</script>
<?php if(!empty($video)||!empty($audio)):?>
<script src="<?= base_url(); ?>assets/vendor/plyr/plyr.min.js"></script>
<script>const player=new Plyr('#player');const audio_player=new Plyr('#audio_player');</script>
<?php endif;?>
<?= $this->general_settings->google_analytics; ?>
<?= $this->general_settings->custom_javascript_codes; ?>
</body>
</html>
