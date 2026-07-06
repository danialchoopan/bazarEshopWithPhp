<?php use App\Helpers\Sanitize; use App\Middleware\Auth; ?>

<?php if ($product): ?>
<div class="row g-4">
    <div class="col-md-6">
        <?php
        $photoUrl = !empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')
            ? '/' . $product['photo']
            : '/img/' . ($product['photo'] ?? 'placeholder.png');
        ?>
        <img src="<?= htmlspecialchars($photoUrl) ?>" class="img-fluid rounded" alt="<?= Sanitize::e($product['name']) ?>" style="max-height: 400px; width: 100%; object-fit: cover;">
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">خانه</a></li>
                <li class="breadcrumb-item"><a href="/products" class="text-decoration-none">محصولات</a></li>
                <?php if (!empty($product['category'])): ?>
                    <li class="breadcrumb-item"><a href="/category/<?= $product['category']['id'] ?>" class="text-decoration-none"><?= Sanitize::e($product['category']['name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= Sanitize::e($product['name']) ?></li>
            </ol>
        </nav>

        <h2 class="fw-bold"><?= Sanitize::e($product['name']) ?></h2>

        <div class="product-price my-3" style="font-size: 1.5rem;">
            <?= number_format($product['price']) ?> تومان
        </div>

        <?php if (($product['stock'] ?? 0) > 0): ?>
            <p><span class="badge badge-in-stock badge-stock">موجود در انبار (<?= $product['stock'] ?> عدد)</span></p>
        <?php else: ?>
            <p><span class="badge badge-out-of-stock badge-stock">ناموجود</span></p>
        <?php endif; ?>

        <div class="my-4">
            <h5 class="fw-bold">توضیحات</h5>
            <p class="text-muted"><?= nl2br(Sanitize::e($product['description'])) ?></p>
        </div>

        <?php if (Auth::check() && ($product['stock'] ?? 0) > 0): ?>
            <form method="post" action="/cart/add" class="d-flex align-items-center gap-2">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-cart-plus me-2"></i>افزودن به سبد خرید
                </button>
            </form>
        <?php elseif (!Auth::check()): ?>
            <a href="/login" class="btn btn-primary btn-lg">برای خرید وارد شوید</a>
        <?php endif; ?>

        <div class="mt-4 text-muted small">
            <i class="bi bi-calendar me-1"></i>
            تاریخ انتشار: <?= date('Y/m/d', $product['created_at']) ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="text-center py-5">
    <p class="text-muted">محصول یافت نشد</p>
    <a href="/products" class="btn btn-primary">بازگشت به محصولات</a>
</div>
<?php endif; ?>
