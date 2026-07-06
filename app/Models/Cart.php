<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Cart
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function findByUser(int $userId): array
    {
        $stmt = self::db()->prepare("
            SELECT c.*, p.name as product_name, p.price as product_price, p.photo as product_photo, p.stock as product_stock
            FROM cart c
            LEFT JOIN products p ON c.product_id = p.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function addItem(int $userId, int $productId, int $quantity = 1): bool
    {
        // Check if item already exists in cart
        $existing = self::findCartItem($userId, $productId);

        if ($existing) {
            $stmt = self::db()->prepare("
                UPDATE cart SET quantity = quantity + :quantity WHERE id = :id
            ");
            return $stmt->execute(['quantity' => $quantity, 'id' => $existing['id']]);
        }

        $stmt = self::db()->prepare("
            INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public static function updateQuantity(int $userId, int $productId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return self::removeItem($userId, $productId);
        }

        $stmt = self::db()->prepare("
            UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id
        ");
        return $stmt->execute([
            'quantity' => $quantity,
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    public static function removeItem(int $userId, int $productId): bool
    {
        $stmt = self::db()->prepare("
            DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id
        ");
        return $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    }

    public static function clear(int $userId): bool
    {
        $stmt = self::db()->prepare("DELETE FROM cart WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    public static function count(int $userId): int
    {
        $stmt = self::db()->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function total(int $userId): float
    {
        $stmt = self::db()->prepare("
            SELECT COALESCE(SUM(p.price * c.quantity), 0)
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return (float) $stmt->fetchColumn();
    }

    private static function findCartItem(int $userId, int $productId): ?array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id
        ");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return $stmt->fetch() ?: null;
    }
}
