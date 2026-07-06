<?php use App\Helpers\Sanitize; use App\Middleware\Auth; use App\Models\Product; ?>

<?php if ($product): ?>
<?php
// Related products (same category, exclude current)
$relatedProducts = [];
if (!empty($product['category_product_id'])) {
    $allRelated = Product::findByCategory($product['category_product_id'], 10);
    $relatedProducts = array_filter($allRelated, fn($p) => $p['id'] != $product['id']);
    $relatedProducts = array_slice($relatedProducts, 0, 4);
}
?>

<div class="row g-4">
    <div class="col-md-6">
        <?php
        $photoUrl = !empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')
            ? '/' . $product['photo']
            : '/img/' . ($product['photo'] ?? 'placeholder.png');
        ?>
        <div class="product-gallery position-relative">
            <img src="<?= htmlspecialchars($photoUrl) ?>" class="img-fluid rounded" alt="<?= Sanitize::e($product['name']) ?>" id="productMainImage" style="width: 100%; max-height: 450px; object-fit: cover; cursor: zoom-in;" onclick="openImageModal(this.src)">
            <?php if (($product['stock'] ?? 0) > 0): ?>
                <span class="position-absolute top-0 end-0 m-3 badge badge-in-stock badge-stock">موجود</span>
            <?php else: ?>
                <span class="position-absolute top-0 end-0 m-3 badge badge-out-of-stock badge-stock">ناموجود</span>
            <?php endif; ?>
        </div>
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

        <h2 class="fw-bold mb-2"><?= Sanitize::e($product['name']) ?></h2>

        <?php if (!empty($product['category'])): ?>
            <span class="badge bg-light text-dark mb-3"><i class="bi bi-tag me-1"></i><?= Sanitize::e($product['category']['name']) ?></span>
        <?php endif; ?>

        <div class="product-price my-3" style="font-size: 1.8rem;">
            <?= number_format($product['price']) ?> <small class="text-muted" style="font-size: 0.9rem;">تومان</small>
        </div>

        <div class="mb-3">
            <?php if (($product['stock'] ?? 0) > 10): ?>
                <span class="badge badge-in-stock badge-stock"><i class="bi bi-check-circle me-1"></i>موجود در انبار (<?= $product['stock'] ?> عدد)</span>
            <?php elseif (($product['stock'] ?? 0) > 0): ?>
                <span class="badge badge-low-stock badge-stock"><i class="bi bi-exclamation-circle me-1"></i>موجودی محدود (<?= $product['stock'] ?> عدد باقیمانده)</span>
            <?php else: ?>
                <span class="badge badge-out-of-stock badge-stock"><i class="bi bi-x-circle me-1"></i>ناموجود</span>
            <?php endif; ?>
        </div>

        <div class="my-4">
            <h5 class="fw-bold mb-3">توضیحات</h5>
            <div class="text-muted" style="line-height: 1.9;"><?= nl2br(Sanitize::e($product['description'])) ?></div>
        </div>

        <?php if (Auth::check() && ($product['stock'] ?? 0) > 0): ?>
            <form method="post" action="/cart/add" class="d-flex align-items-center gap-3 mb-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="d-flex align-items-center border rounded-pill overflow-hidden" style="width: 130px;">
                    <button type="button" class="btn btn-sm px-3 py-2" onclick="changeQty(-1)" style="border: none; background: transparent;"><i class="bi bi-dash"></i></button>
                    <input type="number" name="quantity" id="qty" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control form-control-sm text-center border-0" style="width: 50px; box-shadow: none;">
                    <button type="button" class="btn btn-sm px-3 py-2" onclick="changeQty(1)" style="border: none; background: transparent;"><i class="bi bi-plus"></i></button>
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-cart-plus me-2"></i>افزودن به سبد خرید
                </button>
            </form>
        <?php elseif (!Auth::check()): ?>
            <a href="/login" class="btn btn-primary btn-lg px-4 mb-4">
                <i class="bi bi-box-arrow-in-left me-2"></i>برای خرید وارد شوید
            </a>
        <?php endif; ?>

        <div class="border-top pt-3 mt-3">
            <div class="row text-muted small">
                <div class="col-6 mb-2">
                    <i class="bi bi-truck me-1"></i>ارسال رایگان برای خرید بالای ۵۰۰ هزار تومان
                </div>
                <div class="col-6 mb-2">
                    <i class="bi bi-shield-check me-1"></i>ضمانت اصالت کالا
                </div>
                <div class="col-6 mb-2">
                    <i class="bi bi-arrow-return-left me-1"></i>۷ روز ضمانت بازگشت
                </div>
                <div class="col-6 mb-2">
                    <i class="bi bi-calendar me-1"></i>تاریخ انتشار: <?= date('Y/m/d', $product['created_at']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<div class="mt-5">
    <h5 class="fw-bold mb-3">محصولات مرتبط</h5>
    <div class="row g-3">
        <?php foreach ($relatedProducts as $rp): ?>
            <div class="col-6 col-md-3">
                <a href="/product/<?= $rp['id'] ?>" class="text-decoration-none">
                    <div class="product-card h-100">
                        <?php
                        $rpPhoto = !empty($rp['photo']) && str_starts_with($rp['photo'], 'uploads/')
                            ? '/' . $rp['photo']
                            : '/img/' . ($rp['photo'] ?? 'placeholder.png');
                        ?>
                        <img src="<?= htmlspecialchars($rpPhoto) ?>" class="card-img-top" alt="<?= Sanitize::e($rp['name']) ?>">
                        <div class="card-body">
                            <div class="product-title"><?= Sanitize::e($rp['name']) ?></div>
                            <div class="product-price"><?= number_format($rp['price']) ?> تومان</div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img src="" class="img-fluid rounded" id="modalImage" alt="">
            </div>
        </div>
    </div>
</div>

<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(val, parseInt(input.max)));
    input.value = val;
}
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-box-seam" style="font-size: 4rem; color: #cbd5e1;"></i>
    <h5 class="text-muted mt-3">محصول یافت نشد</h5>
    <a href="/products" class="btn btn-primary mt-2">بازگشت به محصولات</a>
</div>
<?php endif; ?>
