<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (!empty($featured_categories)): ?>
<div class="featured-categories-premium">
  <div class="fcp-scroll">
    <?php foreach ($featured_categories as $category): ?>
    <a href="<?= generate_category_url($category); ?>" class="fcp-item">
      <div class="fcp-img-wrap lazyload" data-bg="<?= get_category_image_url($category); ?>">
        <div class="fcp-overlay"></div>
      </div>
      <span class="fcp-label"><?= category_name($category); ?></span>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<style>
.featured-categories-premium {
  overflow: hidden;
  margin-bottom: 4px;
}
.fcp-scroll {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 12px;
}
.fcp-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 9px;
  text-decoration: none;
  transition: all .22s;
}
.fcp-item:hover { transform: translateY(-4px); }
.fcp-img-wrap {
  width: 100%;
  aspect-ratio: 1 / 1;
  border-radius: 14px;
  background-size: cover;
  background-position: center;
  background-color: #F3F4F6;
  position: relative;
  overflow: hidden;
  border: 1.5px solid #E5E7EB;
  transition: all .22s;
}
.fcp-item:hover .fcp-img-wrap {
  border-color: #F97316;
  box-shadow: 0 4px 16px rgba(249,115,22,.18);
}
.fcp-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(10,22,40,.28) 100%);
  border-radius: 14px;
  transition: all .22s;
}
.fcp-item:hover .fcp-overlay {
  background: linear-gradient(180deg, transparent 20%, rgba(249,115,22,.18) 100%);
}
.fcp-label {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12.5px;
  font-weight: 700;
  color: #374151;
  text-align: center;
  line-height: 1.3;
  transition: color .2s;
}
.fcp-item:hover .fcp-label { color: #F97316; }

@media (max-width: 576px) {
  .fcp-scroll {
    grid-template-columns: repeat(auto-fill, minmax(88px, 1fr));
    gap: 8px;
  }
  .fcp-img-wrap { border-radius: 10px; }
  .fcp-label { font-size: 11.5px; }
}
</style>
<?php endif; ?>
