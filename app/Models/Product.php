<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Product
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch() ?: null;

        if ($product) {
            $product['category'] = Category::find((int) $product['category_product_id']);
        }

        return $product;
    }

    public static function all(int $limit = 100, int $offset = 0, bool $activeOnly = true): array
    {
        $where = $activeOnly ? "WHERE is_active = 1" : "";
        $stmt = self::db()->prepare("SELECT * FROM products {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findByCategory(int $categoryId, int $limit = 100, int $offset = 0): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM products
            WHERE category_product_id = :category_id AND is_active = 1
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function search(string $query, int $limit = 50): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM products
            WHERE (name LIKE :q1 OR description LIKE :q2) AND is_active = 1
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':q1', "%{$query}%", PDO::PARAM_STR);
        $stmt->bindValue(':q2', "%{$query}%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function latest(int $limit = 8): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM products WHERE is_active = 1
            ORDER BY created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO products (name, price, description, photo, category_product_id, stock, slug, is_active, created_at)
            VALUES (:name, :price, :description, :photo, :category_product_id, :stock, :slug, :is_active, NOW())
        ");
        $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? '',
            'photo' => $data['photo'] ?? '',
            'category_product_id' => $data['category_product_id'] ?? 0,
            'stock' => $data['stock'] ?? 0,
            'slug' => $data['slug'] ?? self::generateSlug($data['name']),
            'is_active' => $data['is_active'] ?? 1,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $stmt = self::db()->prepare("UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function count(bool $activeOnly = false): int
    {
        $where = $activeOnly ? "WHERE is_active = 1" : "";
        return (int) self::db()->query("SELECT COUNT(*) FROM products {$where}")->fetchColumn();
    }

    public static function updateStock(int $id, int $quantityChange): bool
    {
        $stmt = self::db()->prepare("
            UPDATE products SET stock = GREATEST(0, stock + :change) WHERE id = :id
        ");
        return $stmt->execute(['change' => $quantityChange, 'id' => $id]);
    }

    public static function lowStock(int $threshold = 5): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM products WHERE stock <= :threshold AND is_active = 1
            ORDER BY stock ASC
        ");
        $stmt->execute(['threshold' => $threshold]);
        return $stmt->fetchAll();
    }

    public static function paginate(int $page = 1, int $perPage = 12, ?int $categoryId = null): array
    {
        $offset = ($page - 1) * $perPage;
        $where = "WHERE is_active = 1";
        $params = [];

        if ($categoryId) {
            $where .= " AND category_product_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $countStmt = self::db()->prepare("SELECT COUNT(*) FROM products {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = self::db()->prepare("
            SELECT * FROM products {$where}
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    private static function generateSlug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}
