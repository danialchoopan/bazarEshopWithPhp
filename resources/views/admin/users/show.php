<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">پروفایل کاربر: <?= Sanitize::e($user['name'] . ' ' . $user['last_name']) ?></h4>
    <a href="/admin/users" class="btn btn-outline-secondary btn-sm">بازگشت</a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">اطلاعات کاربر</h6>
                <p><strong>نام:</strong> <?= Sanitize::e($user['name']) ?></p>
                <p><strong>نام خانوادگی:</strong> <?= Sanitize::e($user['last_name']) ?></p>
                <p><strong>ایمیل:</strong> <?= Sanitize::e($user['email']) ?></p>
                <p><strong>تلفن:</strong> <?= Sanitize::e($user['phone']) ?></p>
                <p><strong>تاریخ عضویت:</strong> <?= date('Y/m/d', strtotime($user['created_at'] ?? 'now')) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">تغییر نقش</h6>
                <form method="post" action="/admin/users/<?= $user['id'] ?>/role">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <select class="form-select" name="role_id">
                            <option value="0" <?= ($user['role'] ?? 0) == 0 ? 'selected' : '' ?>>کاربر عادی</option>
                            <option value="1" <?= ($user['role'] ?? 0) == 1 ? 'selected' : '' ?>>مدیر</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">بروزرسانی نقش</button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">نقش‌های اختصاص یافته</h6>
                <?php if (!empty($userRoles)): ?>
                    <?php foreach ($userRoles as $role): ?>
                        <span class="badge bg-info me-1"><?= Sanitize::e($role['name']) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">نقش خاصی اختصاص نیافته</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- User Orders -->
<div class="mt-4">
    <h6 class="fw-bold mb-3">سفارشات کاربر</h6>
    <?php if (!empty($orders)): ?>
        <div class="admin-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><a href="/admin/orders/<?= $order['id'] ?>"><?= $order['id'] ?></a></td>
                                <td class="fw-bold"><?= number_format($order['total'] ?? 0) ?> تومان</td>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'در انتظار', 'processing' => 'پردازش',
                                        'shipped' => 'ارسال شده', 'delivered' => 'تحویل شده', 'cancelled' => 'لغو شده',
                                    ];
                                    ?>
                                    <span class="status-badge status-<?= $order['status'] ?>"><?= $statusLabels[$order['status']] ?? 'نامشخص' ?></span>
                                </td>
                                <td class="small text-muted"><?= date('Y/m/d', strtotime($order['created_at'] ?? 'now')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted">سفارشی ثبت نشده</p>
    <?php endif; ?>
</div>
