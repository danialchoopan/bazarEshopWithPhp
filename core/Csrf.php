<?php
/**
 * CSRF Protection Helper
 * 
 * Generates and verifies CSRF tokens to prevent Cross-Site Request Forgery attacks.
 * 
 * @package BazarShop\Core\Security
 */

namespace BazarShop\Core;

class Csrf
{
    /**
     * Token name stored in session
     */
    const TOKEN_NAME = 'csrf_token';

    /**
     * Generate a new CSRF token and store it in the session
     * 
     * @return string The generated token
     */
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_NAME] = $token;
        
        return $token;
    }

    /**
     * Get the current CSRF token, generate one if it doesn't exist
     * 
     * @return string The current token
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME])) {
            return self::generateToken();
        }

        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Verify a submitted token against the session token
     * 
     * @param string|null $token The token to verify
     * @return bool True if valid, false otherwise
     */
    public static function verify(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME]) || empty($token)) {
            return false;
        }

        $isValid = hash_equals($_SESSION[self::TOKEN_NAME], $token);
        
        // Regenerate token after use for extra security
        if ($isValid) {
            self::generateToken();
        }

        return $isValid;
    }

    /**
     * Get CSRF token input field HTML
     * 
     * @return string Hidden input field with CSRF token
     */
    public static function tokenField(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . htmlspecialchars($token) . '">';
    }
}
