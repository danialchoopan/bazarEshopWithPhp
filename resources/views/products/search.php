<?php use App\Helpers\Sanitize; ?>

<h4 class="fw-bold mb-4">
    نتایج جستجو: "<?= Sanitize::e($query) ?>"
</h4>

<?php if (empty($results)): ?>
    <div class="text-center py-5">
        <i class="bi bi-search" style="font-size: 3rem; color: #cbd5e1;"></i>
        <p class="text-muted mt-3">نتیجه‌ای یافت نشد</p>
        <a href="/products" class="btn btn-primary btn-sm">مشاهده تمام محصولات</a>
    </div>
<?php else: ?>
    <p class="text-muted mb-3"><?= count($results) ?> محصول یافت شد</p>
    <div class="row g-3">
        <?php foreach ($results as $product): ?>
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
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
