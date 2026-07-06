<?php

namespace App\Controllers\Admin;

use App\Models\Order;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;

class OrderController
{
    public function index(): void
    {
        $status = $_GET['status'] ?? null;
        $orders = $status ? Order::findByStatus($status) : Order::all(200);

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/orders/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function show(string $id): void
    {
        $order = Order::find(Sanitize::int($id));
        if (!$order) {
            header('Location: /admin/orders');
            exit;
        }

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/orders/show.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function updateStatus(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/orders');
            exit;
        }

        $orderId = Sanitize::int($id);
        $status = Sanitize::string($_POST['status'] ?? '');

        if (Order::updateStatus($orderId, $status)) {
            Auth::flash('success', 'وضعیت سفارش بروزرسانی شد');
        } else {
            Auth::flash('danger', 'خطا در بروزرسانی وضعیت');
        }

        header('Location: /admin/orders/' . $orderId);
        exit;
    }
}
