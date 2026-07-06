<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Post
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch() ?: null;

        if ($post) {
            $post['category'] = PostCategory::find((int) $post['category_id']);
        }

        return $post;
    }

    public static function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findByCategory(int $categoryId, int $limit = 50): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM posts WHERE category_id = :category_id
            ORDER BY created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function latest(int $limit = 5): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO posts (title, body, photo, category_id, created_at)
            VALUES (:title, :body, :photo, :category_id, NOW())
        ");
        $stmt->execute([
            'title' => $data['title'],
            'body' => $data['body'],
            'photo' => $data['photo'] ?? '',
            'category_id' => $data['category_id'] ?? 0,
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

        $stmt = self::db()->prepare("UPDATE posts SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    }

    public static function search(string $query, int $limit = 20): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM posts WHERE title LIKE :q OR body LIKE :q
            ORDER BY created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':q', "%{$query}%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
