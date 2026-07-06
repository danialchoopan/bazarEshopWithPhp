<?php

namespace App\Helpers;

use App\Config\App;

class Upload
{
    private static string $basePath = '';

    private static function getBasePath(): string
    {
        if (self::$basePath === '') {
            self::$basePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';
        }
        return self::$basePath;
    }

    /**
     * Allowed MIME types for images
     */
    private static array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /**
     * Upload a file and return the relative path
     */
    public static function upload(array $file, string $subdirectory = 'general'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!in_array($file['type'], self::$allowedMimes)) {
            return null;
        }

        $maxSize = App::getInstance()->get('upload_max_size', 5242880);
        if ($file['size'] > $maxSize) {
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        $year = date('Y');
        $month = date('m');
        $targetDir = self::getBasePath() . DIRECTORY_SEPARATOR . $subdirectory . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/' . $subdirectory . '/' . $year . '/' . $month . '/' . $filename;
        }

        return null;
    }

    /**
     * Delete a file by relative path
     */
    public static function delete(string $relativePath): bool
    {
        $fullPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Get the full absolute path from a relative upload path
     */
    public static function getAbsolutePath(string $relativePath): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath;
    }
}
