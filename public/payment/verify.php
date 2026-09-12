<?php
/**
 * Payment Verification Callback Handler
 * 
 * Handles ZarinPal payment callback and verifies transactions.
 */

require_once __DIR__ . '/../../core/ZarinPalPayment.php';
require_once __DIR__ . '/../../core/ErrorHandler.php';
require_once __DIR__ . '/../../core/CouponManager.php';

use BazarShop\Core\ZarinPalPayment;
use BazarShop\Core\ErrorHandler;
use BazarShop\Core\CouponManager;

// Initialize error handler
$isDev = getenv('APP_ENV') === 'development';
ErrorHandler::init($isDev);

// Get callback parameters
$authority = $_GET['Authority'] ?? '';
$status = $_GET['Status'] ?? '';

if (!$authority || $status !== 'OK') {
    // Payment failed or cancelled
    header('Location: /payment_failed.php');
    exit;
}

try {
    // Get order info from session or database
    session_start();
    $orderId = $_SESSION['pending_order_id'] ?? 0;
    
    if (!$orderId) {
        throw new Exception('سفارش یافت نشد');
    }

    // Get order amount from database
    require_once __DIR__ . '/../../core/Database.php';
    use BazarShop\Core\Database;
    
    $db = new Database();
    $stmt = $db->getConnection()->prepare("SELECT total_amount FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('سفارش یافت نشد');
    }

    $amount = (int)$order['total_amount'];

    // Verify payment with ZarinPal
    $zarinpal = new ZarinPalPayment();
    $result = $zarinpal->verify($authority, $amount);

    if ($result['success']) {
        // Payment successful
        $zarinpal->updateOrderStatus($orderId, 'paid', $result['ref_id']);

        // Increment coupon usage if applicable
        if (!empty($_SESSION['coupon_code'])) {
            $couponManager = new CouponManager();
            $couponManager->incrementUsage($_SESSION['coupon_code']);
        }

        // Decrement product stock
        $stmt = $db->getConnection()->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $couponManager = new CouponManager();
        foreach ($items as $item) {
            $couponManager->decrementStock($item['product_id'], $item['quantity']);
        }

        // Clear session
        unset($_SESSION['pending_order_id']);
        unset($_SESSION['coupon_code']);

        // Redirect to success page
        header("Location: /payment_success.php?ref_id={$result['ref_id']}");
    } else {
        // Payment verification failed
        $zarinpal->updateOrderStatus($orderId, 'failed');
        header('Location: /payment_failed.php?error=' . urlencode($result['message']));
    }

} catch (\Exception $e) {
    ErrorHandler::log("Payment verification error: " . $e->getMessage(), 'ERROR');
    header('Location: /payment_failed.php?error=خطا_در_سیستم_پرداخت');
}
