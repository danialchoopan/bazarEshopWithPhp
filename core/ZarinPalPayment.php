<?php
/**
 * ZarinPal Payment Gateway Integration
 * 
 * Handles payment requests, verification, and callbacks.
 * 
 * @package BazarShop\Core\Payment
 */

namespace BazarShop\Core;

use BazarShop\Core\Database;

class ZarinPalPayment
{
    /**
     * ZarinPal API URL (Sandbox)
     */
    private const API_URL = 'https://sandbox.zarinpal.com/pg/v4/payment';
    
    /**
     * ZarinPal API URL (Production)
     */
    private const API_URL_PRODUCTION = 'https://api.zarinpal.com/pg/v4/payment';

    /**
     * Merchant ID (Sandbox)
     */
    private const MERCHANT_SANDBOX = '879daed9-18e0-4a64-b01f-2ed3c0a739b5';

    /**
     * Database connection
     */
    private Database $db;

    /**
     * Merchant ID
     */
    private string $merchantId;

    /**
     * Is sandbox mode
     */
    private bool $isSandbox;

    /**
     * Callback URL
     */
    private string $callbackUrl;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = new Database();
        
        // Load config from .env or use defaults
        $this->isSandbox = getenv('ZARINPAL_SANDBOX') !== 'false';
        $this->merchantId = getenv('ZARINPAL_MERCHANT_ID') ?: self::MERCHANT_SANDBOX;
        $this->callbackUrl = (getenv('APP_URL') ?: 'http://localhost/bazarEshopWithPhp/public') . '/payment/verify.php';
    }

    /**
     * Create payment request
     * 
     * @param array $data Payment data
     * @return array ['success' => bool, 'authority' => string|null, 'url' => string|null, 'message' => string]
     */
    public function request(array $data): array
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'amount' => (int)$data['amount'], // Amount in Tomans
                'description' => $data['description'] ?? 'پرداخت سفارش',
                'callback_url' => $this->callbackUrl,
                'metadata' => [
                    'order_id' => $data['order_id'] ?? null,
                    'mobile' => $data['mobile'] ?? null,
                    'email' => $data['email'] ?? null,
                ]
            ];

            $ch = curl_init(self::API_URL . '/request.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                return [
                    'success' => false,
                    'authority' => null,
                    'url' => null,
                    'message' => 'خطا در ارتباط با زرین‌پال'
                ];
            }

            $result = json_decode($response, true);

            if ($result['errors']['code'] === 100) {
                $authority = $result['data']['authority'];
                
                // Save authority to database for this order
                if (!empty($data['order_id'])) {
                    $this->saveAuthority($data['order_id'], $authority);
                }

                return [
                    'success' => true,
                    'authority' => $authority,
                    'url' => $this->getPaymentUrl($authority),
                    'message' => 'درخواست پرداخت با موفقیت ایجاد شد'
                ];
            } else {
                return [
                    'success' => false,
                    'authority' => null,
                    'url' => null,
                    'message' => $result['errors']['message'] ?? 'خطا در ایجاد درخواست پرداخت'
                ];
            }

        } catch (\Exception $e) {
            ErrorHandler::log("ZarinPal request failed: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'authority' => null,
                'url' => null,
                'message' => 'خطا در سیستم پرداخت'
            ];
        }
    }

    /**
     * Verify payment after callback
     * 
     * @param string $authority Payment authority
     * @param int $amount Payment amount
     * @return array ['success' => bool, 'ref_id' => string|null, 'message' => string, 'card_hash' => string|null]
     */
    public function verify(string $authority, int $amount): array
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'amount' => $amount,
                'authority' => $authority,
            ];

            $ch = curl_init(self::API_URL . '/verification.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                return [
                    'success' => false,
                    'ref_id' => null,
                    'message' => 'خطا در تایید پرداخت',
                    'card_hash' => null
                ];
            }

            $result = json_decode($response, true);

            if ($result['errors']['code'] === 100) {
                return [
                    'success' => true,
                    'ref_id' => (string)$result['data']['ref_id'],
                    'message' => 'پرداخت با موفقیت تایید شد',
                    'card_hash' => $result['data']['card_hash'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'ref_id' => null,
                    'message' => $result['errors']['message'] ?? 'پرداخت تایید نشد',
                    'card_hash' => null
                ];
            }

        } catch (\Exception $e) {
            ErrorHandler::log("ZarinPal verify failed: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'ref_id' => null,
                'message' => 'خطا در سیستم پرداخت',
                'card_hash' => null
            ];
        }
    }

    /**
     * Get payment gateway URL
     * 
     * @param string $authority Payment authority
     * @return string Payment URL
     */
    public function getPaymentUrl(string $authority): string
    {
        if ($this->isSandbox) {
            return "https://sandbox.zarinpal.com/pg/StartPay/{$authority}";
        } else {
            return "https://www.zarinpal.com/pg/StartPay/{$authority}";
        }
    }

    /**
     * Save payment authority to database
     * 
     * @param int $orderId Order ID
     * @param string $authority Payment authority
     * @return bool Success status
     */
    private function saveAuthority(int $orderId, string $authority): bool
    {
        try {
            $stmt = $this->db->getConnection()->prepare("
                UPDATE orders 
                SET transaction_id = ?, payment_status = 'pending'
                WHERE id = ?
            ");
            return $stmt->execute([$authority, $orderId]);
        } catch (\Exception $e) {
            ErrorHandler::log("Failed to save authority: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Update order payment status
     * 
     * @param int $orderId Order ID
     * @param string $status Payment status
     * @param string|null $refId Reference ID
     * @return bool Success status
     */
    public function updateOrderStatus(int $orderId, string $status, ?string $refId = null): bool
    {
        try {
            $sql = "UPDATE orders SET payment_status = ?, updated_at = CURRENT_TIMESTAMP";
            $params = [$status];

            if ($refId) {
                $sql .= ", transaction_id = ?";
                $params[] = $refId;
            }

            $sql .= " WHERE id = ?";
            $params[] = $orderId;

            $stmt = $this->db->getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            ErrorHandler::log("Failed to update order status: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Calculate shipping cost
     * 
     * @param string $city Destination city
     * @param float $weight Total weight in kg
     * @return float Shipping cost in Tomans
     */
    public function calculateShipping(string $city, float $weight): float
    {
        // Base shipping cost
        $baseCost = 35000; // 35,000 Tomans base
        
        // Tehran has lower shipping cost
        if ($city === 'tehran' || $city === 'تهران') {
            $baseCost = 25000;
        }

        // Add cost per kg after first kg
        $additionalWeight = max(0, $weight - 1);
        $additionalCost = $additionalWeight * 15000;

        return $baseCost + $additionalCost;
    }
}
