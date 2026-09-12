<?php
/**
 * Database Connection Handler
 * 
 * This file handles database connections for both MySQL and SQLite.
 * It reads configuration from environment variables or .env file.
 * 
 * Features:
 * - Auto-detection of database driver (MySQL or SQLite)
 * - PDO with error handling
 * - Support for .env file configuration
 * - Secure connection settings
 * 
 * @package BazarShop
 * @version 2.0
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Load environment variables from .env file
 * This allows easy configuration without modifying code
 */
function loadEnv($path = null) {
    $envFile = $path ?? dirname(__DIR__) . '/.env';
    
    if (!file_exists($envFile)) {
        // If .env doesn't exist, try to use default values
        return false;
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            
            // Only set if not already defined
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
    
    return true;
}

// Load environment variables
loadEnv();

/**
 * Application Configuration Constants
 * These can be overridden in .env file
 */
if (!defined('APP_NAME')) {
    define('APP_NAME', getenv('APP_NAME') ?: 'بازار');
}

if (!defined('APP_URL')) {
    define('APP_URL', getenv('APP_URL') ?: 'http://localhost/bazarEshopWithPhp/public/');
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
}

/**
 * Database Configuration
 * Supports both MySQL and SQLite drivers
 */
$dbDriver = getenv('DB_DRIVER') ?: 'mysql';
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_DATABASE') ?: 'em-bazar-shop-db';
$dbUsername = getenv('DB_USERNAME') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';

/**
 * Establish Database Connection
 * 
 * Uses PDO (PHP Data Objects) for database abstraction
 * Provides consistent interface regardless of database type
 * 
 * @global PDO $db_connection Database connection instance
 */
try {
    if ($dbDriver === 'sqlite') {
        /**
         * SQLite Connection
         * - File-based database, no server required
         * - Perfect for development and small deployments
         * - Database file stored in database/ directory
         */
        $dbPath = $dbName;
        
        // If path is relative, make it absolute
        if (!strpos($dbPath, '/') === 0 && !strpos($dbPath, '\\') === 0) {
            $dbPath = dirname(__DIR__) . '/database/' . $dbPath;
        }
        
        // Create database directory if it doesn't exist
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        // Create SQLite connection
        $db_connection = new PDO("sqlite:$dbPath");
        
        // Enable foreign keys for SQLite
        $db_connection->exec("PRAGMA foreign_keys = ON");
        
    } else {
        /**
         * MySQL/MariaDB Connection
         * - Full-featured relational database
         * - Recommended for production environments
         * - Better performance for large datasets
         */
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return associative arrays
            PDO::ATTR_EMULATE_PREPARES => false               // Use native prepared statements
        ];
        
        // Only add MYSQL_ATTR_INIT_COMMAND if MySQL extension is loaded
        if (extension_loaded('pdo_mysql')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
        }
        
        $db_connection = new PDO($dsn, $dbUsername, $dbPassword, $options);
    }
    
    // Set error mode for SQLite (if not already set)
    if ($dbDriver === 'sqlite') {
        $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    /**
     * Database Connection Error Handler
     * 
     * In debug mode: Show detailed error information
     * In production mode: Show generic error message
     */
    if (APP_DEBUG) {
        echo "<div style='text-align: center; padding: 50px; font-family: Arial;'>";
        echo "<h2 style='color: #dc3545;'>خطا در اتصال به پایگاه داده</h2>";
        echo "<p><strong>پیام خطا:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>درایور:</strong> " . htmlspecialchars($dbDriver) . "</p>";
        echo "<p><strong>راه‌حل پیشنهادی:</strong></p>";
        echo "<ul style='text-align: right; display: inline-block;'>";
        echo "<li>بررسی کنید فایل .env وجود داشته باشد</li>";
        echo "<li>اطلاعات اتصال به دیتابیس را بررسی کنید</li>";
        echo "<li>مطمئن شوید سرور دیتابیس در حال اجرا است</li>";
        echo "<li>برای SQLite: بررسی کنید پوشه database قابل نوشتن باشد</li>";
        echo "</ul>";
        echo "</div>";
        exit();
    } else {
        // Production mode - generic error
        echo "<div style='text-align: center; padding: 50px;'>";
        echo "<h2>خطایی رخ داده است</h2>";
        echo "<p>لطفاً با مدیر سایت تماس بگیرید</p>";
        echo "</div>";
        exit();
    }
}
