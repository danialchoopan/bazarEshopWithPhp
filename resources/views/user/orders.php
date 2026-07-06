<?php use App\Helpers\Sanitize; ?>

<h4 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i>سفارشات من</h4>

<?php if (empty($orders)): ?>
    <div class="text-center py-5">
        <i class="bi bi-receipt" style="font-size: 3rem; color: #cbd5e1;"></i>
        <p class="text-muted mt-3">سفارشی ثبت نشده است</p>
        <a href="/products" class="btn btn-primary btn-sm">مشاهده محصولات</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>تاریخ</th>
                    <th>مبلغ</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= date('Y/m/d H:i', strtotime($order['created_at'] ?? 'now')) ?></td>
                        <td class="fw-bold"><?= number_format($order['total'] ?? 0) ?> تومان</td>
                        <td>
                            <?php
                            $statusLabels = [
                                'pending' => ['در انتظار تایید', 'warning'],
                                'processing' => ['در حال پردازش', 'info'],
                                'shipped' => ['ارسال شده', 'primary'],
                                'delivered' => ['تحویل شده', 'success'],
                                'cancelled' => ['لغو شده', 'danger'],
                            ];
                            $status = $order['status'] ?? 'pending';
                            $label = $statusLabels[$status] ?? ['نامشخص', 'secondary'];
                            ?>
                            <span class="badge bg-<?= $label[1] ?>"><?= $label[0] ?></span>
                        </td>
                        <td>
                            <a href="/orders/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">جزئیات</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
