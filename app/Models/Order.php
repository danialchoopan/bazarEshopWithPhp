<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Order
{
    private static PDO $db;

    private static function db(): PDO
    {
        return self::$db ??= Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch() ?: null;

        if ($order) {
            $order['items'] = OrderItem::findByOrder($id);
            $order['user'] = User::find((int) $order['user_id']);
        }

        return $order;
    }

    public static function findByUser(int $userId, int $limit = 50): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM orders WHERE user_id = :user_id
            ORDER BY created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findByStatus(string $status, int $limit = 100): array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM orders WHERE status = :status
            ORDER BY created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = self::db()->prepare("
            SELECT o.*, u.name, u.last_name, u.email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO orders (user_id, total, status, user_address, description, phone, created_at)
            VALUES (:user_id, :total, 'pending', :user_address, :description, :phone, NOW())
        ");
        $stmt->execute([
            'user_id' => $data['user_id'],
            'total' => $data['total'] ?? 0,
            'user_address' => $data['user_address'],
            'description' => $data['description'] ?? '',
            'phone' => $data['phone'],
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $stmt = self::db()->prepare("
            UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id
        ");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public static function setTrackingCode(int $id, string $trackingCode): bool
    {
        $stmt = self::db()->prepare("
            UPDATE orders SET tracking_code = :tracking, updated_at = NOW() WHERE id = :id
        ");
        return $stmt->execute(['tracking' => $trackingCode, 'id' => $id]);
    }

    public static function count(?string $status = null): int
    {
        if ($status) {
            $stmt = self::db()->prepare("SELECT COUNT(*) FROM orders WHERE status = :status");
            $stmt->execute(['status' => $status]);
            return (int) $stmt->fetchColumn();
        }
        return (int) self::db()->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    public static function totalRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        $where = "WHERE status != 'cancelled'";
        $params = [];

        if ($startDate) {
            $where .= " AND created_at >= :start";
            $params['start'] = $startDate;
        }
        if ($endDate) {
            $where .= " AND created_at <= :end";
            $params['end'] = $endDate;
        }

        $stmt = self::db()->prepare("SELECT COALESCE(SUM(total), 0) FROM orders {$where}");
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public static function revenueByMonth(int $months = 12): array
    {
        $stmt = self::db()->prepare("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as order_count,
                SUM(total) as revenue
            FROM orders
            WHERE status != 'cancelled'
            AND created_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function statusCounts(): array
    {
        $stmt = self::db()->query("
            SELECT status, COUNT(*) as count
            FROM orders
            GROUP BY status
        ");
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['status']] = (int) $row['count'];
        }
        return $result;
    }

    public static function recent(int $limit = 10): array
    {
        $stmt = self::db()->prepare("
            SELECT o.*, u.name, u.last_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
