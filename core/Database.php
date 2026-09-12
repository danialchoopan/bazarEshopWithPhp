<?php
/**
 * Database Connection Class
 * 
 * Modern object-oriented database connection handler.
 * Supports both MySQL and SQLite drivers.
 * 
 * @package BazarShop\Core
 * @version 2.0
 */

namespace BazarShop\Core;

use PDO;
use PDOException;

class Database
{
    /**
     * Database connection instance
     * 
     * @var PDO|null
     */
    private static ?PDO $connection = null;
    
    /**
     * Current database driver
     * 
     * @var string
     */
    private string $driver;
    
    /**
     * Get database connection instance (Singleton pattern)
     * 
     * @return PDO Database connection
     * @throws PDOException If connection fails
     */
    public function getConnection(): PDO
    {
        if (self::$connection === null) {
            $this->connect();
        }
        
        return self::$connection;
    }
    
    /**
     * Establish database connection
     * 
     * @return void
     * @throws PDOException If connection fails
     */
    private function connect(): void
    {
        // Load environment variables
        $this->loadEnv();
        
        $this->driver = getenv('DB_DRIVER') ?: 'mysql';
        $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
        $dbPort = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_DATABASE') ?: 'bazar.db';
        $dbUsername = getenv('DB_USERNAME') ?: 'root';
        $dbPassword = getenv('DB_PASSWORD') ?: '';
        
        try {
            if ($this->driver === 'sqlite') {
                $this->connectSQLite($dbName);
            } else {
                $this->connectMySQL($dbHost, $dbPort, $dbName, $dbUsername, $dbPassword);
            }
        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }
    
    /**
     * Connect to SQLite database
     * 
     * @param string $dbName Database file path
     * @return void
     * @throws PDOException If connection fails
     */
    private function connectSQLite(string $dbName): void
    {
        $dbPath = $dbName;
        
        // If path is relative, make it absolute
        if (!str_starts_with($dbPath, '/') && !str_starts_with($dbPath, '\\')) {
            $dbPath = dirname(__DIR__, 2) . '/database/' . $dbName;
        }
        
        // Create database directory if it doesn't exist
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        // Create SQLite connection
        self::$connection = new PDO("sqlite:$dbPath");
        
        // Enable foreign keys for SQLite
        self::$connection->exec("PRAGMA foreign_keys = ON");
        
        // Set error mode and fetch mode
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    
    /**
     * Connect to MySQL database
     * 
     * @param string $host Database host
     * @param string $port Database port
     * @param string $name Database name
     * @param string $username Database username
     * @param string $password Database password
     * @return void
     * @throws PDOException If connection fails
     */
    private function connectMySQL(string $host, string $port, string $name, string $username, string $password): void
    {
        $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return associative arrays
            PDO::ATTR_EMULATE_PREPARES => false,              // Use native prepared statements
        ];
        
        // Add MySQL-specific init command if PDO extension is loaded
        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
        }
        
        self::$connection = new PDO($dsn, $username, $password, $options);
    }
    
    /**
     * Handle database connection errors
     * 
     * @param PDOException $e The exception thrown
     * @return void
     */
    private function handleConnectionError(PDOException $e): void
    {
        $isDebug = filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
        
        if ($isDebug) {
            echo "<div style='text-align: center; padding: 50px; font-family: Arial;'>";
            echo "<h2 style='color: #dc3545;'>خطا در اتصال به پایگاه داده</h2>";
            echo "<p><strong>پیام خطا:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>درایور:</strong> " . htmlspecialchars($this->driver) . "</p>";
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
    
    /**
     * Load environment variables from .env file
     * 
     * @param string|null $path Path to .env file
     * @return bool True if loaded successfully
     */
    private function loadEnv(?string $path = null): bool
    {
        $envFile = $path ?? dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envFile)) {
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
    
    /**
     * Get the current database driver
     * 
     * @return string Current driver (mysql or sqlite)
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
    
    /**
     * Close database connection
     * 
     * @return void
     */
    public function close(): void
    {
        self::$connection = null;
    }
}
