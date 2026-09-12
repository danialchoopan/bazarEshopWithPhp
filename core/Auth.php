<?php
/**
 * Role-Based Access Control (RBAC) System
 * 
 * Manages user roles and permissions for secure access control.
 * 
 * @package BazarShop\Core\Auth
 */

namespace BazarShop\Core;

class Auth
{
    /**
     * Available roles
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_CUSTOMER = 'customer';

    /**
     * Check if user is logged in
     * 
     * @return bool True if logged in
     */
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     * 
     * @return int|null User ID or null
     */
    public static function userId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     * 
     * @return string|null User role or null
     */
    public static function userRole(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Get current user email
     * 
     * @return string|null User email or null
     */
    public static function userEmail(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_email'] ?? null;
    }

    /**
     * Login user
     * 
     * @param int $userId User ID
     * @param string $email User email
     * @param string $role User role
     * @return void
     */
    public static function login(int $userId, string $email, string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['logged_in_at'] = time();
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session data
        $_SESSION = [];

        // Delete the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Check if user has specific role
     * 
     * @param string $role Role to check
     * @return bool True if user has role
     */
    public static function hasRole(string $role): bool
    {
        $userRole = self::userRole();
        
        if ($userRole === null) {
            return false;
        }

        return $userRole === $role;
    }

    /**
     * Check if user is admin
     * 
     * @return bool True if admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is editor or admin
     * 
     * @return bool True if editor or admin
     */
    public static function isEditorOrAdmin(): bool
    {
        $role = self::userRole();
        return in_array($role, [self::ROLE_ADMIN, self::ROLE_EDITOR], true);
    }

    /**
     * Require user to be logged in, redirect otherwise
     * 
     * @param string $redirectUrl URL to redirect to
     * @return void
     */
    public static function requireLogin(string $redirectUrl = '/login.php'): void
    {
        if (!self::check()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Require user to have specific role, redirect otherwise
     * 
     * @param string $role Required role
     * @param string $redirectUrl URL to redirect to
     * @return void
     */
    public static function requireRole(string $role, string $redirectUrl = '/unauthorized.php'): void
    {
        self::requireLogin();

        if (!self::hasRole($role)) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Require user to be admin, redirect otherwise
     * 
     * @param string $redirectUrl URL to redirect to
     * @return void
     */
    public static function requireAdmin(string $redirectUrl = '/unauthorized.php'): void
    {
        self::requireRole(self::ROLE_ADMIN, $redirectUrl);
    }
}
