<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class OrderItem
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function findByOrder(int $orderId): array
    {
        $stmt = self::db()->prepare("
            SELECT oi.*, p.name as product_name, p.photo as product_photo
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");
        $stmt->execute([
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
        return (int) self::db()->lastInsertId();
    }

    /**
     * Migrate legacy serialized order products to order_items
     */
    public static function migrateLegacyOrder(int $orderId, string $serializedProducts): void
    {
        $productIds = @unserialize($serializedProducts);
        if (!is_array($productIds)) {
            return;
        }

        foreach ($productIds as $productId) {
            $product = Product::find((int) $productId);
            if ($product) {
                self::create([
                    'order_id' => $orderId,
                    'product_id' => (int) $productId,
                    'quantity' => 1,
                    'price' => $product['price'],
                ]);
            }
        }
    }
}
