<?php

namespace App\Config;

class App
{
    private static ?App $instance = null;
    private array $config = [];

    private function __construct()
    {
        $this->config = [
            'name' => $_ENV['APP_NAME'] ?? 'بازار',
            'url' => $_ENV['APP_URL'] ?? 'http://localhost/bazarEshopWithPhp/public/',
            'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'database' => $_ENV['DB_DATABASE'] ?? 'em-reza-shop-db',
                'username' => $_ENV['DB_USERNAME'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ],
            'upload_max_size' => (int) ($_ENV['UPLOAD_MAX_SIZE'] ?? 5242880),
        ];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
}
