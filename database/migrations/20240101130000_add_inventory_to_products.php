<?php
/**
 * Migration: Add Inventory Management to Products
 * 
 * Adds stock quantity and low stock threshold to products table.
 */

class AddInventoryToProducts
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
        // Check if columns already exist
        $tableInfo = $this->db->getConnection()->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
        $columns = array_column($tableInfo, 'name');

        if (!in_array('stock_quantity', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE products ADD COLUMN stock_quantity INTEGER DEFAULT 100");
            echo "  → Added stock_quantity column\n";
        }

        if (!in_array('low_stock_threshold', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE products ADD COLUMN low_stock_threshold INTEGER DEFAULT 10");
            echo "  → Added low_stock_threshold column\n";
        }

        if (!in_array('is_active', $columns)) {
            $this->db->getConnection()->exec("ALTER TABLE products ADD COLUMN is_active BOOLEAN DEFAULT 1");
            echo "  → Added is_active column\n";
        }

        // Create index for low stock queries
        $this->db->getConnection()->exec("CREATE INDEX IF NOT EXISTS idx_products_stock ON products(stock_quantity)");
        echo "  → Created stock index\n";
    }

    /**
     * Rollback the migration
     * Note: SQLite doesn't support DROP COLUMN in older versions
     */
    public function down(): void
    {
        echo "  → Note: Cannot drop columns in SQLite without recreating table\n";
        echo "  → Manual cleanup may be required\n";
    }
}
