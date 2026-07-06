<?php

namespace App\Middleware;

use App\Models\User;

class Auth
{
    /**
     * Start session if not already started
     */
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get the currently logged-in user
     */
    public static function user(): ?array
    {
        self::ensureSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if a user is logged in
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Get user ID
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int) $user['id'] : null;
    }

    /**
     * Login a user (store in session)
     */
    public static function login(array $userData): void
    {
        self::ensureSession();
        $_SESSION['user'] = $userData;
    }

    /**
     * Logout the current user
     */
    public static function logout(): void
    {
        self::ensureSession();
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    /**
     * Require user to be logged in, redirect to login page if not
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            self::redirect('/login');
        }
    }

    /**
     * Require user to be an admin, redirect if not
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();

        $user = self::user();
        if (!$user || !in_array($user['role'] ?? 0, [1])) {
            self::redirect('/');
        }
    }

    /**
     * Require user to have a specific permission
     */
    public static function requirePermission(string $permission): void
    {
        self::requireAdmin();

        $user = self::user();
        if ($user && User::hasPermission($user['id'], $permission)) {
            return;
        }

        self::redirect('/admin');
    }

    /**
     * Redirect to a URL relative to APP_URL
     */
    public static function redirect(string $path): void
    {
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/bazarEshopWithPhp/public/';
        header('Location: ' . rtrim($baseUrl, '/') . $path);
        exit;
    }

    /**
     * Flash a message to the session
     */
    public static function flash(string $type, string $message): void
    {
        self::ensureSession();
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Get and clear flash message
     */
    public static function getFlash(): ?array
    {
        self::ensureSession();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
