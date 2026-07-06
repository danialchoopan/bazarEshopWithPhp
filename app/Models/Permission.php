<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Permission
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM permissions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM permissions WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return self::db()->query("SELECT * FROM permissions ORDER BY name ASC")->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO permissions (name, description) VALUES (:name, :description)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM role_permissions WHERE permission_id = :id");
        $stmt->execute(['id' => $id]);

        $stmt = self::db()->prepare("DELETE FROM permissions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM permissions")->fetchColumn();
    }

    /**
     * Seed default permissions
     */
    public static function seed(): void
    {
        $defaults = [
            ['name' => 'manage_products', 'description' => 'مدیریت محصولات'],
            ['name' => 'manage_orders', 'description' => 'مدیریت سفارشات'],
            ['name' => 'manage_users', 'description' => 'مدیریت کاربران'],
            ['name' => 'manage_posts', 'description' => 'مدیریت پست‌ها'],
            ['name' => 'view_dashboard', 'description' => 'مشاهده داشبورد'],
            ['name' => 'manage_settings', 'description' => 'مدیریت تنظیمات'],
            ['name' => 'manage_roles', 'description' => 'مدیریت نقش‌ها'],
        ];

        foreach ($defaults as $perm) {
            if (!self::findByName($perm['name'])) {
                self::create($perm);
            }
        }
    }
}
