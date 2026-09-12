<?php
/**
 * Custom Error Handler and Logger
 * 
 * Handles errors gracefully, logs them to file, and shows friendly error pages.
 * 
 * @package BazarShop\Core\Error
 */

namespace BazarShop\Core;

class ErrorHandler
{
    /**
     * Log file path
     */
    private static string $logFile = __DIR__ . '/../logs/error.log';

    /**
     * Whether we're in development mode
     */
    private static bool $isDevelopment = true;

    /**
     * Initialize error handler
     * 
     * @param bool $isDevelopment Development mode flag
     * @return void
     */
    public static function init(bool $isDevelopment = true): void
    {
        self::$isDevelopment = $isDevelopment;

        // Set error reporting based on environment
        if ($isDevelopment) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        // Set custom error handler
        set_error_handler([self::class, 'handleError']);
        
        // Set custom exception handler
        set_exception_handler([self::class, 'handleException']);
        
        // Register shutdown function for fatal errors
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    /**
     * Handle standard PHP errors
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile File where error occurred
     * @param int $errline Line number
     * @return bool True if handled, false otherwise
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Respect error_reporting level
        if (!(error_reporting() & $errno)) {
            return false;
        }

        self::logError($errstr, $errfile, $errline, $errno);

        if (!self::$isDevelopment) {
            self::showErrorPage('An error occurred. Please try again later.');
        }

        return true;
    }

    /**
     * Handle uncaught exceptions
     * 
     * @param \Throwable $exception The exception
     * @return void
     */
    public static function handleException(\Throwable $exception): void
    {
        self::logError(
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getCode(),
            get_class($exception)
        );

        if (!self::$isDevelopment) {
            self::showErrorPage('An unexpected error occurred. Please try again later.');
        } else {
            echo "<h2>Uncaught Exception: " . htmlspecialchars(get_class($exception)) . "</h2>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " (line {$exception->getLine()})</p>";
            echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        }
    }

    /**
     * Handle fatal errors
     * 
     * @return void
     */
    public static function handleFatalError(): void
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::logError($error['message'], $error['file'], $error['line'], $error['type'], 'Fatal Error');

            if (!self::$isDevelopment) {
                self::showErrorPage('A critical error occurred. The application will be restarted.');
            }
        }
    }

    /**
     * Log error to file
     * 
     * @param string $message Error message
     * @param string $file File where error occurred
     * @param int $line Line number
     * @param int $code Error code
     * @param string $type Error type
     * @return void
     */
    private static function logError(string $message, string $file, int $line, int $code, string $type = 'Error'): void
    {
        // Ensure log directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $logEntry = sprintf(
            "[%s] %s: %s in %s:%d [Code: %d] [IP: %s] [URL: %s] [UA: %s]\n",
            $timestamp,
            $type,
            $message,
            $file,
            $line,
            $code,
            $ip,
            $url,
            $userAgent
        );

        // Append to log file
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Show friendly error page
     * 
     * @param string $message Error message to display
     * @return void
     */
    private static function showErrorPage(string $message): void
    {
        http_response_code(500);
        
        // Check if we have a custom error page
        $customErrorPage = __DIR__ . '/../public/500.html';
        if (file_exists($customErrorPage)) {
            include $customErrorPage;
        } else {
            // Default error page
            echo '<!DOCTYPE html>';
            echo '<html lang="fa" dir="rtl">';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '<title>خطا - Bazar Shop</title>';
            echo '<style>';
            echo 'body{font-family:Tahoma,sans-serif;background:#f3f4f6;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}';
            echo '.error-box{background:#fff;padding:2rem;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);text-align:center;max-width:400px}';
            echo 'h1{color:#ef4444;font-size:2rem;margin:0 0 1rem}';
            echo 'p{color:#6b7280;margin:0 0 1.5rem}';
            echo 'a{color:#6366f1;text-decoration:none}';
            echo 'a:hover{texttext-decoration:underline}';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<div class="error-box">';
            echo '<h1>⚠️ خطا</h1>';
            echo '<p>' . htmlspecialchars($message) . '</p>';
            echo '<a href="/">بازگشت به صفحه اصلی</a>';
            echo '</div>';
            echo '</body>';
            echo '</html>';
        }

        exit;
    }

    /**
     * Log a custom message
     * 
     * @param string $message Message to log
     * @param string $level Log level (INFO, WARNING, ERROR)
     * @return void
     */
    public static function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}\n";
        
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
