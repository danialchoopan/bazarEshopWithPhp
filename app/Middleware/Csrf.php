<?php

namespace App\Middleware;

class Csrf
{
    /**
     * Generate and store a CSRF token in the session
     */
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Get the current CSRF token (generates if missing)
     */
    public static function token(): string
    {
        return self::generateToken();
    }

    /**
     * Render a hidden input field with the CSRF token
     */
    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validate the CSRF token from a request
     */
    public static function verify(?string $token = null): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $token ?? ($_POST['csrf_token'] ?? '');

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Regenerate the CSRF token (call after successful form submission)
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
