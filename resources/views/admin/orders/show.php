<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">سفارش #<?= $order['id'] ?></h4>
    <a href="/admin/orders" class="btn btn-outline-secondary btn-sm">بازگشت</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">اقلام سفارش</h6>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>محصول</th>
                                <th>تعداد</th>
                                <th>قیمت واحد</th>
                                <th>جمع</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] ?? [] as $item): ?>
                                <tr>
                                    <td><?= Sanitize::e($item['product_name'] ?? 'نامشخص') ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price']) ?> تومان</td>
                                    <td class="fw-bold"><?= number_format($item['price'] * $item['quantity']) ?> تومان</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">جمع کل</td>
                                <td class="fw-bold text-success" style="font-size: 1.1rem;"><?= number_format($order['total'] ?? 0) ?> تومان</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Order Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">اطلاعات سفارش</h6>
                <p class="mb-2"><strong>کاربر:</strong> <?= Sanitize::e(($order['user']['name'] ?? '') . ' ' . ($order['user']['last_name'] ?? '')) ?></p>
                <p class="mb-2"><strong>تلفن:</strong> <?= Sanitize::e($order['phone']) ?></p>
                <p class="mb-2"><strong>آدرس:</strong> <?= Sanitize::e($order['user_address']) ?></p>
                <?php if (!empty($order['description'])): ?>
                    <p class="mb-2"><strong>توضیحات:</strong> <?= Sanitize::e($order['description']) ?></p>
                <?php endif; ?>
                <p class="mb-2"><strong>تاریخ:</strong> <?= date('Y/m/d H:i', strtotime($order['created_at'] ?? 'now')) ?></p>
                <?php if (!empty($order['tracking_code'])): ?>
                    <p class="mb-0"><strong>کد رهگیری:</strong> <?= Sanitize::e($order['tracking_code']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Update -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">تغییر وضعیت</h6>
                <form method="post" action="/admin/orders/<?= $order['id'] ?>/status">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <select class="form-select" name="status">
                            <?php
                            $statuses = [
                                'pending' => 'در انتظار تایید',
                                'processing' => 'در حال پردازش',
                                'shipped' => 'ارسال شده',
                                'delivered' => 'تحویل شده',
                                'cancelled' => 'لغو شده',
                            ];
                            foreach ($statuses as $value => $label):
                            ?>
                                <option value="<?= $value ?>" <?= ($order['status'] ?? '') === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>بروزرسانی وضعیت
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
