<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Category
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM category_product WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return self::db()->query("SELECT * FROM category_product ORDER BY create_at DESC")->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO category_product (name, photo, create_at)
            VALUES (:name, :photo, :create_at)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'photo' => $data['photo'] ?? '',
            'create_at' => time(),
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $stmt = self::db()->prepare("UPDATE category_product SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM category_product WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function productCount(int $categoryId): int
    {
        $stmt = self::db()->prepare("SELECT COUNT(*) FROM products WHERE category_product_id = :id");
        $stmt->execute(['id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    public static function count(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM category_product")->fetchColumn();
    }
}
