<?php
/**
 * Migration: Create Coupons Table
 * 
 * Creates table for discount coupon system.
 */

class CreateCouponsTable
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
        $sql = "CREATE TABLE IF NOT EXISTS coupons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code VARCHAR(50) NOT NULL UNIQUE,
            description TEXT,
            discount_type VARCHAR(20) NOT NULL DEFAULT 'percentage',
            discount_value DECIMAL(10,2) NOT NULL,
            min_order_amount DECIMAL(10,2) DEFAULT 0,
            max_discount_amount DECIMAL(10,2) DEFAULT NULL,
            usage_limit INTEGER DEFAULT NULL,
            used_count INTEGER DEFAULT 0,
            expires_at DATETIME DEFAULT NULL,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        $this->db->getConnection()->exec($sql);

        // Create index on code for faster lookups
        $this->db->getConnection()->exec("CREATE INDEX IF NOT EXISTS idx_coupons_code ON coupons(code)");
        $this->db->getConnection()->exec("CREATE INDEX IF NOT EXISTS idx_coupons_active ON coupons(is_active)");

        echo "  → Created coupons table\n";
    }

    /**
     * Rollback the migration
     */
    public function down(): void
    {
        $this->db->getConnection()->exec("DROP TABLE IF EXISTS coupons");
        echo "  → Dropped coupons table\n";
    }
}
