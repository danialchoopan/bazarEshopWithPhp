<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Middleware\Auth;

class UserController
{
    public function profile(): void
    {
        $user = Auth::user();

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/user/profile.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function orders(): void
    {
        $userId = Auth::id();
        $orders = Order::findByUser($userId);

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/user/orders.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }
}
