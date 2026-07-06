<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class PostCategory
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM category_post WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return self::db()->query("SELECT * FROM category_post ORDER BY created_at DESC")->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO category_post (name, created_at) VALUES (:name, NOW())
        ");
        $stmt->execute(['name' => $data['name']]);
        return (int) self::db()->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM category_post WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function postCount(int $categoryId): int
    {
        $stmt = self::db()->prepare("SELECT COUNT(*) FROM posts WHERE category_id = :id");
        $stmt->execute(['id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    public static function count(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM category_post")->fetchColumn();
    }
}
