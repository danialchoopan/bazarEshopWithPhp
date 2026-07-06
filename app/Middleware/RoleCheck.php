<?php

namespace App\Middleware;

class RoleCheck
{
    /**
     * Check if the current user has a specific role
     */
    public static function hasRole(string $roleName): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Legacy: role=1 means admin
        if (($user['role'] ?? 0) == 1 && $roleName === 'admin') {
            return true;
        }

        // Check user_roles table if available
        return self::checkUserRole($user['id'], $roleName);
    }

    /**
     * Check if the current user has a specific permission
     */
    public static function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Legacy admin has all permissions
        if (($user['role'] ?? 0) == 1) {
            return true;
        }

        return \App\Models\User::hasPermission($user['id'], $permission);
    }

    /**
     * Require a specific role, redirect if not authorized
     */
    public static function requireRole(string $roleName): void
    {
        if (!self::hasRole($roleName)) {
            Auth::flash('danger', 'شما دسترسی به این بخش ندارید');
            Auth::redirect('/admin');
        }
    }

    /**
     * Require a specific permission
     */
    public static function requirePermission(string $permission): void
    {
        if (!self::hasPermission($permission)) {
            Auth::flash('danger', 'شما دسترسی به این بخش ندارید');
            Auth::redirect('/admin');
        }
    }

    /**
     * Check user_roles table for role assignment
     */
    private static function checkUserRole(int $userId, string $roleName): bool
    {
        try {
            $pdo = \App\Database\Connection::getInstance();
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM user_roles ur
                JOIN roles r ON ur.role_id = r.id
                WHERE ur.user_id = :user_id AND r.name = :role_name
            ");
            $stmt->execute(['user_id' => $userId, 'role_name' => $roleName]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            // Table may not exist yet, fall back to legacy role check
            return false;
        }
    }
}
