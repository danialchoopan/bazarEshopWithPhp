<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;

class CheckoutController
{
    public function index(): void
    {
        $userId = Auth::id();
        $items = Cart::findByUser($userId);
        $total = Cart::total($userId);

        if (empty($items)) {
            Auth::flash('warning', 'سبد خرید شما خالی است');
            header('Location: /products');
            exit;
        }

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/checkout/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function placeOrder(): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی. لطفا دوباره تلاش کنید');
            header('Location: /checkout');
            exit;
        }

        $userId = Auth::id();
        $items = Cart::findByUser($userId);

        if (empty($items)) {
            Auth::flash('warning', 'سبد خرید شما خالی است');
            header('Location: /products');
            exit;
        }

        $phone = Sanitize::phone($_POST['phone'] ?? '');
        $address = Sanitize::string($_POST['address'] ?? '');
        $description = Sanitize::string($_POST['description'] ?? '');

        // Validate
        $validation = Sanitize::validate($_POST, [
            'phone' => 'required|phone',
            'address' => 'required|min:10',
        ]);

        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                Auth::flash('danger', $error);
                break;
            }
            header('Location: /checkout');
            exit;
        }

        $total = Cart::total($userId);

        // Create order
        $orderId = Order::create([
            'user_id' => $userId,
            'total' => $total,
            'user_address' => $address,
            'description' => $description,
            'phone' => $phone,
        ]);

        // Create order items and update stock
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['product_price'],
            ]);

            // Decrement stock
            Product::updateStock($item['product_id'], -$item['quantity']);
        }

        // Clear cart
        Cart::clear($userId);
        Csrf::regenerate();

        Auth::flash('success', 'سفارش شما با شماره #' . $orderId . ' ثبت شد');
        header('Location: /orders');
        exit;
    }
}
