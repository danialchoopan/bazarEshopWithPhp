<?php
/**
 * Coupon System Manager
 * 
 * Handles coupon creation, validation, and application.
 * 
 * @package BazarShop\Core\Commerce
 */

namespace BazarShop\Core;

use BazarShop\Core\Database;

class CouponManager
{
    /**
     * Database connection
     */
    private Database $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Validate and apply a coupon code
     * 
     * @param string $code Coupon code
     * @param float $orderTotal Order total amount
     * @return array ['valid' => bool, 'discount' => float, 'message' => string]
     */
    public function validateAndApply(string $code, float $orderTotal): array
    {
        $code = strtoupper(trim($code));

        // Get coupon from database
        $stmt = $this->db->getConnection()->prepare("
            SELECT * FROM coupons 
            WHERE code = ? AND is_active = 1
        ");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$coupon) {
            return [
                'valid' => false,
                'discount' => 0,
                'message' => 'کد تخفیف نامعتبر است.'
            ];
        }

        // Check expiration
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            return [
                'valid' => false,
                'discount' => 0,
                'message' => 'کد تخفیف منقضی شده است.'
            ];
        }

        // Check usage limit
        if ($coupon['usage_limit'] !== null && $coupon['used_count'] >= $coupon['usage_limit']) {
            return [
                'valid' => false,
                'discount' => 0,
                'message' => 'کد تخفیف به حد مجاز استفاده رسیده است.'
            ];
        }

        // Check minimum order amount
        if ($orderTotal < $coupon['min_order_amount']) {
            return [
                'valid' => false,
                'discount' => 0,
                'message' => 'حداقل مبلغ سفارش برای این کد تخفیف ' . number_format($coupon['min_order_amount']) . ' تومان است.'
            ];
        }

        // Calculate discount
        $discount = 0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($orderTotal * $coupon['discount_value']) / 100;
            
            // Apply max discount cap if set
            if ($coupon['max_discount_amount'] !== null && $discount > $coupon['max_discount_amount']) {
                $discount = $coupon['max_discount_amount'];
            }
        } else {
            // Fixed amount discount
            $discount = $coupon['discount_value'];
        }

        // Ensure discount doesn't exceed order total
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return [
            'valid' => true,
            'discount' => $discount,
            'message' => 'کد تخفیف با موفقیت اعمال شد.',
            'code' => $code,
            'description' => $coupon['description']
        ];
    }

    /**
     * Increment coupon usage count
     * 
     * @param string $code Coupon code
     * @return bool Success status
     */
    public function incrementUsage(string $code): bool
    {
        try {
            $stmt = $this->db->getConnection()->prepare("
                UPDATE coupons 
                SET used_count = used_count + 1, updated_at = CURRENT_TIMESTAMP
                WHERE code = ?
            ");
            return $stmt->execute([strtoupper($code)]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create a new coupon
     * 
     * @param array $data Coupon data
     * @return int|false Coupon ID or false on failure
     */
    public function create(array $data)
    {
        try {
            $stmt = $this->db->getConnection()->prepare("
                INSERT INTO coupons (
                    code, description, discount_type, discount_value,
                    min_order_amount, max_discount_amount, usage_limit,
                    expires_at, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                strtoupper($data['code']),
                $data['description'] ?? null,
                $data['discount_type'] ?? 'percentage',
                $data['discount_value'],
                $data['min_order_amount'] ?? 0,
                $data['max_discount_amount'] ?? null,
                $data['usage_limit'] ?? null,
                $data['expires_at'] ?? null,
                $data['is_active'] ?? 1
            ]);

            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            ErrorHandler::log("Failed to create coupon: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Get all coupons
     * 
     * @param bool $includeExpired Include expired coupons
     * @return array List of coupons
     */
    public function getAll(bool $includeExpired = false): array
    {
        $sql = "SELECT * FROM coupons ORDER BY created_at DESC";
        
        if (!$includeExpired) {
            $sql .= " WHERE (expires_at IS NULL OR expires_at > datetime('now'))";
        }

        $stmt = $this->db->getConnection()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update coupon
     * 
     * @param int $id Coupon ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(int $id, array $data): bool
    {
        try {
            $fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }

            $values[] = $id;

            $sql = "UPDATE coupons SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            
            $stmt = $this->db->getConnection()->prepare($sql);
            return $stmt->execute($values);
        } catch (\Exception $e) {
            ErrorHandler::log("Failed to update coupon {$id}: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Delete coupon
     * 
     * @param int $id Coupon ID
     * @return bool Success status
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->getConnection()->prepare("DELETE FROM coupons WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\Exception $e) {
            ErrorHandler::log("Failed to delete coupon {$id}: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Get low stock alerts for products
     * 
     * @return array Products with low stock
     */
    public function getLowStockProducts(): array
    {
        $stmt = $this->db->getConnection()->query("
            SELECT id, name, stock_quantity, low_stock_threshold
            FROM products
            WHERE stock_quantity <= low_stock_threshold
            AND is_active = 1
            ORDER BY stock_quantity ASC
        ");

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Check product availability
     * 
     * @param int $productId Product ID
     * @param int $quantity Requested quantity
     * @return array ['available' => bool, 'stock' => int, 'message' => string]
     */
    public function checkAvailability(int $productId, int $quantity): array
    {
        $stmt = $this->db->getConnection()->prepare("
            SELECT stock_quantity, name FROM products WHERE id = ?
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$product) {
            return [
                'available' => false,
                'stock' => 0,
                'message' => 'محصول یافت نشد.'
            ];
        }

        if ($product['stock_quantity'] < $quantity) {
            return [
                'available' => false,
                'stock' => $product['stock_quantity'],
                'message' => "موجودی کافی نیست. موجودی فعلی: {$product['stock_quantity']}"
            ];
        }

        return [
            'available' => true,
            'stock' => $product['stock_quantity'],
            'message' => 'موجودی کافی است.'
        ];
    }

    /**
     * Decrement product stock after order
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity to decrement
     * @return bool Success status
     */
    public function decrementStock(int $productId, int $quantity): bool
    {
        try {
            $stmt = $this->db->getConnection()->prepare("
                UPDATE products 
                SET stock_quantity = stock_quantity - ?
                WHERE id = ? AND stock_quantity >= ?
            ");
            return $stmt->execute([$quantity, $productId, $quantity]);
        } catch (\Exception $e) {
            ErrorHandler::log("Failed to decrement stock for product {$productId}: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}
