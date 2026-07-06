<?php use App\Helpers\Sanitize; ?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <h1>به <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?> خوش آمدید</h1>
    <p class="mb-4">بهترین محصولات با بهترین کیفیت و قیمت</p>
    <a href="/products" class="btn btn-light btn-lg px-4">مشاهده محصولات</a>
</div>

<!-- Categories -->
<?php if (!empty($categories)): ?>
<div class="mb-4">
    <h4 class="fw-bold mb-3">دسته‌بندی‌ها</h4>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($categories as $cat): ?>
            <a href="/category/<?= $cat['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                <?= Sanitize::e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Latest Products -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">جدیدترین محصولات</h4>
        <a href="/products" class="btn btn-outline-primary btn-sm">مشاهده همه</a>
    </div>
    <div class="row g-3">
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="/product/<?= $product['id'] ?>" class="text-decoration-none">
                    <div class="product-card h-100">
                        <?php
                        $photoUrl = !empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')
                            ? '/' . $product['photo']
                            : '/img/' . ($product['photo'] ?? 'placeholder.png');
                        ?>
                        <img src="<?= htmlspecialchars($photoUrl) ?>" class="card-img-top" alt="<?= Sanitize::e($product['name']) ?>">
                        <div class="card-body">
                            <div class="product-title"><?= Sanitize::e($product['name']) ?></div>
                            <div class="product-price"><?= number_format($product['price']) ?> تومان</div>
                            <?php if (($product['stock'] ?? 0) > 0): ?>
                                <span class="badge badge-in-stock badge-stock">موجود</span>
                            <?php else: ?>
                                <span class="badge badge-out-of-stock badge-stock">ناموجود</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
