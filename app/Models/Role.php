<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Role
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM roles WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return self::db()->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO roles (name, description) VALUES (:name, :description)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::db()->prepare("
            UPDATE roles SET name = :name, description = :description WHERE id = :id
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM role_permissions WHERE role_id = :id");
        $stmt->execute(['id' => $id]);

        $stmt = self::db()->prepare("DELETE FROM user_roles WHERE role_id = :id");
        $stmt->execute(['id' => $id]);

        $stmt = self::db()->prepare("DELETE FROM roles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getPermissions(int $roleId): array
    {
        $stmt = self::db()->prepare("
            SELECT p.* FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
        ");
        $stmt->execute(['role_id' => $roleId]);
        return $stmt->fetchAll();
    }

    public static function setPermissions(int $roleId, array $permissionIds): bool
    {
        $stmt = self::db()->prepare("DELETE FROM role_permissions WHERE role_id = :role_id");
        $stmt->execute(['role_id' => $roleId]);

        $stmt = self::db()->prepare("
            INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)
        ");

        foreach ($permissionIds as $permissionId) {
            $stmt->execute(['role_id' => $roleId, 'permission_id' => $permissionId]);
        }

        return true;
    }

    public static function count(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    }
}
