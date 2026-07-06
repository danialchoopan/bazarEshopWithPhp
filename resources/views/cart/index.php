<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<h4 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>سبد خرید</h4>

<?php if (empty($items)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 3rem; color: #cbd5e1;"></i>
        <p class="text-muted mt-3">سبد خرید شما خالی است</p>
        <a href="/products" class="btn btn-primary">مشاهده محصولات</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <?php foreach ($items as $item): ?>
                <div class="cart-item" data-product-id="<?= $item['product_id'] ?>">
                    <div class="d-flex align-items-center gap-3">
                        <?php
                        $photoUrl = !empty($item['product_photo']) && str_starts_with($item['product_photo'], 'uploads/')
                            ? '/' . $item['product_photo']
                            : '/img/' . ($item['product_photo'] ?? 'placeholder.png');
                        ?>
                        <img src="<?= htmlspecialchars($photoUrl) ?>" alt="<?= Sanitize::e($item['product_name']) ?>">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?= Sanitize::e($item['product_name']) ?></h6>
                            <div class="text-muted small"><?= number_format($item['product_price']) ?> تومان</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form method="post" action="/cart/update" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <input type="hidden" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">-</button>
                            </form>
                            <span class="fw-bold mx-2"><?= $item['quantity'] ?></span>
                            <form method="post" action="/cart/update" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">+</button>
                            </form>
                        </div>
                        <div class="fw-bold text-nowrap"><?= number_format($item['product_price'] * $item['quantity']) ?> تومان</div>
                        <form method="post" action="/cart/remove">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">خلاصه سفارش</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">تعداد اقلام</span>
                        <span><?= count($items) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">جمع کل</span>
                        <span class="fw-bold text-success" style="font-size: 1.2rem;"><?= number_format($total) ?> تومان</span>
                    </div>
                    <a href="/checkout" class="btn btn-primary w-100">تکمیل خرید</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
