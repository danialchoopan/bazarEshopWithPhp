<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Middleware\Auth;
use App\Helpers\Sanitize;

class CartController
{
    public function index(): void
    {
        $userId = Auth::id();
        $items = Cart::findByUser($userId);
        $total = Cart::total($userId);

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/cart/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function add(): void
    {
        $userId = Auth::id();
        $productId = Sanitize::int($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
        $quantity = max(1, Sanitize::int($_POST['quantity'] ?? 1));

        if ($productId > 0) {
            $product = Product::find($productId);
            if ($product && $product['stock'] >= $quantity) {
                Cart::addItem($userId, $productId, $quantity);
                Auth::flash('success', 'محصول به سبد خرید اضافه شد');
            } else {
                Auth::flash('danger', 'محصول موجود نیست');
            }
        }

        // Return JSON for AJAX requests
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cart_count' => Cart::count($userId),
            ]);
            exit;
        }

        header('Location: /cart');
        exit;
    }

    public function update(): void
    {
        $userId = Auth::id();
        $productId = Sanitize::int($_POST['product_id'] ?? 0);
        $quantity = Sanitize::int($_POST['quantity'] ?? 1);

        if ($productId > 0) {
            Cart::updateQuantity($userId, $productId, max(1, $quantity));
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total' => Cart::total($userId),
                'cart_count' => Cart::count($userId),
            ]);
            exit;
        }

        header('Location: /cart');
        exit;
    }

    public function remove(): void
    {
        $userId = Auth::id();
        $productId = Sanitize::int($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            Cart::removeItem($userId, $productId);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total' => Cart::total($userId),
                'cart_count' => Cart::count($userId),
            ]);
            exit;
        }

        header('Location: /cart');
        exit;
    }
}
