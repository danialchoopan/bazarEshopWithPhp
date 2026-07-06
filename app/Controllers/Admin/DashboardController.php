<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Post;
use App\Middleware\Auth;

class DashboardController
{
    public function index(): void
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::totalRevenue(),
            'pending_orders' => Order::count('pending'),
            'low_stock' => Product::lowStock(5),
        ];

        $recentOrders = Order::recent(10);
        $statusCounts = Order::statusCounts();
        $revenueByMonth = Order::revenueByMonth(12);

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/dashboard/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function stats(): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'revenue_by_month' => Order::revenueByMonth(12),
            'status_counts' => Order::statusCounts(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::totalRevenue(),
        ]);
        exit;
    }
}
