<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<h4 class="fw-bold mb-4"><i class="bi bi-credit-card me-2"></i>تکمیل خرید</h4>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="post" action="/checkout" class="checkout-form">
            <?= Csrf::field() ?>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">اطلاعات ارسال</h5>

                    <div class="mb-3">
                        <label for="phone" class="form-label">شماره تلفن <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?= Sanitize::e($_POST['phone'] ?? '') ?>" required
                               placeholder="09123456789">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">آدرس ارسال <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required
                                  placeholder="آدرس کامل پستی"><?= Sanitize::e($_POST['address'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">توضیحات (اختیاری)</label>
                        <textarea class="form-control" id="description" name="description" rows="2"
                                  placeholder="توضیحات سفارش"><?= Sanitize::e($_POST['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-check-circle me-2"></i>ثبت سفارش
            </button>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">اقلام سفارش</h5>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="small"><?= Sanitize::e($item['product_name']) ?></span>
                            <span class="text-muted small">x<?= $item['quantity'] ?></span>
                        </div>
                        <span class="small fw-bold"><?= number_format($item['product_price'] * $item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">جمع کل</span>
                    <span class="fw-bold text-success" style="font-size: 1.2rem;"><?= number_format($total) ?> تومان</span>
                </div>
            </div>
        </div>
    </div>
</div>
