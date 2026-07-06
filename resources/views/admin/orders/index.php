<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">مدیریت سفارشات</h4>
    <div class="d-flex gap-2">
        <a href="/admin/orders" class="btn btn-sm <?= !isset($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?>">همه</a>
        <a href="/admin/orders?status=pending" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">در انتظار</a>
        <a href="/admin/orders?status=processing" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'processing' ? 'btn-info' : 'btn-outline-info' ?>">پردازش</a>
        <a href="/admin/orders?status=shipped" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'shipped' ? 'btn-primary' : 'btn-outline-primary' ?>">ارسال شده</a>
        <a href="/admin/orders?status=delivered" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'delivered' ? 'btn-success' : 'btn-outline-success' ?>">تحویل شده</a>
        <a href="/admin/orders?status=cancelled" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' ?>">لغو شده</a>
    </div>
</div>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>کاربر</th>
                    <th>مبلغ</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= Sanitize::e(($order['name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></td>
                        <td class="fw-bold"><?= number_format($order['total'] ?? 0) ?> تومان</td>
                        <td>
                            <?php
                            $statusLabels = [
                                'pending' => 'در انتظار',
                                'processing' => 'پردازش',
                                'shipped' => 'ارسال شده',
                                'delivered' => 'تحویل شده',
                                'cancelled' => 'لغو شده',
                            ];
                            ?>
                            <span class="status-badge status-<?= $order['status'] ?>"><?= $statusLabels[$order['status']] ?? 'نامشخص' ?></span>
                        </td>
                        <td class="small text-muted"><?= date('Y/m/d', strtotime($order['created_at'] ?? 'now')) ?></td>
                        <td>
                            <a href="/admin/orders/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">سفارشی وجود ندارد</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
