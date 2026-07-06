<?php use App\Helpers\Sanitize; ?>

<h4 class="fw-bold mb-4">داشبورد مدیریت</h4>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">کل محصولات</div>
                    <div class="stat-value"><?= $stats['total_products'] ?></div>
                </div>
                <div class="stat-icon blue"><i class="bi bi-box-seam"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">کل سفارشات</div>
                    <div class="stat-value"><?= $stats['total_orders'] ?></div>
                </div>
                <div class="stat-icon green"><i class="bi bi-receipt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">کل کاربران</div>
                    <div class="stat-value"><?= $stats['total_users'] ?></div>
                </div>
                <div class="stat-icon yellow"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">درآمد کل</div>
                    <div class="stat-value" style="font-size: 1.3rem;"><?= number_format($stats['total_revenue']) ?></div>
                    <div class="stat-label">تومان</div>
                </div>
                <div class="stat-icon red"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">درآمد ماهانه</h6>
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">وضعیت سفارشات</h6>
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders + Low Stock -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="admin-table">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0">آخرین سفارشات</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>کاربر</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="/admin/orders/<?= $order['id'] ?>" class="text-decoration-none"><?= $order['id'] ?></a></td>
                                <td><?= Sanitize::e(($order['name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></td>
                                <td class="fw-bold"><?= number_format($order['total'] ?? 0) ?></td>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        'pending' => ['در انتظار', 'warning'],
                                        'processing' => ['پردازش', 'info'],
                                        'shipped' => ['ارسال شده', 'primary'],
                                        'delivered' => ['تحویل شده', 'success'],
                                        'cancelled' => ['لغو شده', 'danger'],
                                    ];
                                    $s = $statusLabels[$order['status']] ?? ['نامشخص', 'secondary'];
                                    ?>
                                    <span class="status-badge status-<?= $order['status'] ?>"><?= $s[0] ?></span>
                                </td>
                                <td class="small text-muted"><?= date('m/d', strtotime($order['created_at'] ?? 'now')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">سفارشی ثبت نشده</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle text-warning me-1"></i>موجودی کم</h6>
                <?php if (empty($stats['low_stock'])): ?>
                    <p class="text-muted small">همه محصولات موجودی کافی دارند</p>
                <?php else: ?>
                    <?php foreach ($stats['low_stock'] as $product): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small"><?= Sanitize::e($product['name']) ?></span>
                            <span class="badge bg-warning text-dark"><?= $product['stock'] ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueData = <?= json_encode($revenueByMonth) ?>;
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revenueData.map(d => d.month),
            datasets: [{
                label: 'درآمد (تومان)',
                data: revenueData.map(d => d.revenue || 0),
                backgroundColor: 'rgba(37, 99, 235, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Status Chart
    const statusData = <?= json_encode($statusCounts) ?>;
    const statusLabels = {
        'pending': 'در انتظار',
        'processing': 'پردازش',
        'shipped': 'ارسال شده',
        'delivered': 'تحویل شده',
        'cancelled': 'لغو شده'
    };
    const statusColors = {
        'pending': '#f59e0b',
        'processing': '#3b82f6',
        'shipped': '#8b5cf6',
        'delivered': '#10b981',
        'cancelled': '#ef4444'
    };

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(k => statusLabels[k] || k),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: Object.keys(statusData).map(k => statusColors[k] || '#94a3b8'),
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
