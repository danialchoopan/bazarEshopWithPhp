<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class User
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO users (name, last_name, phone, email, password, role, status, created_at)
            VALUES (:name, :last_name, :phone, :email, :password, :role, 'active', NOW())
        ");
        $stmt->execute([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 0,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $fields[] = "$key = :$key";
                $params[$key] = password_hash($value, PASSWORD_DEFAULT);
            } else {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        $stmt = self::db()->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = self::db()->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    /**
     * Authenticate user with email/password
     */
    public static function authenticate(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Migrate plaintext passwords
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                self::update($user['id'], ['password' => $password]);
            }
            unset($user['password']);
            return $user;
        }
        return null;
    }

    /**
     * Check if user has a specific permission
     */
    public static function hasPermission(int $userId, string $permission): bool
    {
        try {
            $stmt = self::db()->prepare("
                SELECT COUNT(*) FROM user_roles ur
                JOIN role_permissions rp ON ur.role_id = rp.role_id
                JOIN permissions p ON rp.permission_id = p.id
                WHERE ur.user_id = :user_id AND p.name = :permission
            ");
            $stmt->execute(['user_id' => $userId, 'permission' => $permission]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get user's roles
     */
    public static function getRoles(int $userId): array
    {
        try {
            $stmt = self::db()->prepare("
                SELECT r.* FROM roles r
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = :user_id
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Assign a role to a user
     */
    public static function assignRole(int $userId, int $roleId): bool
    {
        try {
            $stmt = self::db()->prepare("
                INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)
            ");
            return $stmt->execute(['user_id' => $userId, 'role_id' => $roleId]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Remove a role from a user
     */
    public static function removeRole(int $userId, int $roleId): bool
    {
        try {
            $stmt = self::db()->prepare("
                DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id
            ");
            return $stmt->execute(['user_id' => $userId, 'role_id' => $roleId]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}
