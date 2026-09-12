<?php
/**
 * Migration: Add Order Tracking and Status
 * 
 * Enhances orders table with tracking and status management.
 */

class AddOrderTracking
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Run the migration
     */
    public function up(): void
    {
        $tableInfo = $this->db->getConnection()->query("PRAGMA table_info(orders)")->fetchAll(PDO::FETCH_ASSOC);
        $columns = array_column($tableInfo, 'name');

        // Add tracking number
        if (!in_array('tracking_number', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100)");
            echo "  → Added tracking_number column\n";
        }

        // Add shipping address fields if not exist
        if (!in_array('shipping_address', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN shipping_address TEXT");
            echo "  → Added shipping_address column\n";
        }

        if (!in_array('shipping_city', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN shipping_city VARCHAR(100)");
            echo "  → Added shipping_city column\n";
        }

        if (!in_array('shipping_postal_code', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN shipping_postal_code VARCHAR(20)");
            echo "  → Added shipping_postal_code column\n";
        }

        // Add payment info
        if (!in_array('payment_method', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'zarinpal'");
            echo "  → Added payment_method column\n";
        }

        if (!in_array('payment_status', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN payment_status VARCHAR(20) DEFAULT 'pending'");
            echo "  → Added payment_status column\n";
        }

        if (!in_array('transaction_id', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN transaction_id VARCHAR(100)");
            echo "  → Added transaction_id column\n";
        }

        // Add coupon usage
        if (!in_array('coupon_code', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN coupon_code VARCHAR(50)");
            echo "  → Added coupon_code column\n";
        }

        if (!in_array('discount_amount', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0");
            echo "  → Added discount_amount column\n";
        }

        // Add shipping cost
        if (!in_array('shipping_cost', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE orders ADD COLUMN shipping_cost DECIMAL(10,2) DEFAULT 0");
            echo "  → Added shipping_cost column\n";
        }

        // Create indexes
        $this->db->getConnection()->exec("CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status)");
        $this->db->getConnection()->exec("CREATE INDEX IF NOT EXISTS idx_orders_tracking ON orders(tracking_number)");
        $this->db->getConnection()->exec("CREATE INDEX IF NOT EXISTS idx_orders_payment ON orders(payment_status)");
        
        echo "  → Created order indexes\n";
    }

    /**
     * Rollback the migration
     */
    public function down(): void
    {
        echo "  → Note: Cannot drop columns in SQLite without recreating table\n";
    }
}
