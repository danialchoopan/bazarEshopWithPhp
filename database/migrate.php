<?php
/**
 * Database Migration System
 * 
 * Manages database schema versions and migrations.
 * 
 * Usage:
 *   php migrate.php up     - Run all pending migrations
 *   php migrate.php down   - Rollback last migration
 *   php migrate.php status - Show migration status
 * 
 * @package BazarShop\Database
 */

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';

use BazarShop\Core\Database;

class MigrationManager
{
    /**
     * Migrations table name
     */
    private const TABLE = 'migrations';

    /**
     * Migrations directory
     */
    private string $migrationsDir;

    /**
     * Database connection
     */
    private Database $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->migrationsDir = __DIR__ . '/migrations';
        $this->db = new Database();
        $this->ensureMigrationsTable();
    }

    /**
     * Ensure migrations table exists
     */
    private function ensureMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this::TABLE} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $this->db->getConnection()->exec($sql);
        } catch (\Exception $e) {
            // Table might already exist or SQLite syntax issue
            echo "Note: Could not create migrations table: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Get list of executed migrations
     * 
     * @return array Executed migration names
     */
    private function getExecutedMigrations(): array
    {
        try {
            $stmt = $this->db->getConnection()->query("SELECT migration FROM {$this::TABLE} ORDER BY id");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get list of migration files
     * 
     * @return array Migration file names
     */
    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsDir)) {
            return [];
        }

        $files = scandir($this->migrationsDir);
        $files = array_diff($files, ['.', '..']);
        
        // Filter only PHP files and sort
        $files = array_filter($files, fn($f) => str_ends_with($f, '.php'));
        sort($files);

        return $files;
    }

    /**
     * Run all pending migrations
     */
    public function up(): void
    {
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();
        $pending = array_diff($files, $executed);

        if (empty($pending)) {
            echo "✓ No pending migrations.\n";
            return;
        }

        echo "Running " . count($pending) . " migration(s)...\n\n";

        foreach ($pending as $file) {
            echo "→ Running: {$file}\n";
            
            try {
                require_once $this->migrationsDir . '/' . $file;
                
                // Extract class name from filename (e.g., 20240101_create_users_table.php -> CreateUsersTable)
                $className = $this->filenameToClassName($file);
                
                if (!class_exists($className)) {
                    throw new \Exception("Migration class '{$className}' not found in {$file}");
                }

                $migration = new $className($this->db);
                $migration->up();

                // Record migration
                $stmt = $this->db->getConnection()->prepare("INSERT INTO {$this::TABLE} (migration) VALUES (?)");
                $stmt->execute([$file]);

                echo "✓ Completed: {$file}\n\n";
            } catch (\Exception $e) {
                echo "✗ Failed: {$file}\n";
                echo "  Error: " . $e->getMessage() . "\n\n";
                echo "Stopping migrations.\n";
                exit(1);
            }
        }

        echo "✓ All migrations completed successfully.\n";
    }

    /**
     * Rollback last migration
     */
    public function down(): void
    {
        $executed = $this->getExecutedMigrations();

        if (empty($executed)) {
            echo "✓ No migrations to rollback.\n";
            return;
        }

        $lastMigration = end($executed);
        echo "→ Rolling back: {$lastMigration}\n";

        try {
            require_once $this->migrationsDir . '/' . $lastMigration;
            
            $className = $this->filenameToClassName($lastMigration);
            
            if (!class_exists($className)) {
                throw new \Exception("Migration class '{$className}' not found");
            }

            $migration = new $className($this->db);
            $migration->down();

            // Remove migration record
            $stmt = $this->db->getConnection()->prepare("DELETE FROM {$this::TABLE} WHERE migration = ?");
            $stmt->execute([$lastMigration]);

            echo "✓ Rolled back: {$lastMigration}\n";
        } catch (\Exception $e) {
            echo "✗ Failed to rollback: {$lastMigration}\n";
            echo "  Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Show migration status
     */
    public function status(): void
    {
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();

        echo "\nMigration Status:\n";
        echo str_repeat('=', 60) . "\n";

        if (empty($files)) {
            echo "No migrations found.\n";
            return;
        }

        foreach ($files as $file) {
            $status = in_array($file, $executed) ? '✓ Applied' : '○ Pending';
            $date = substr($file, 0, 14);
            $name = preg_replace('/^\d{14}_/', '', $file);
            $name = str_replace('.php', '', $name);
            
            printf("%-20s %-35s %s\n", $date, $name, $status);
        }

        echo str_repeat('=', 60) . "\n";
        printf("Total: %d | Applied: %d | Pending: %d\n", 
            count($files), 
            count($executed), 
            count($files) - count($executed)
        );
        echo "\n";
    }

    /**
     * Convert filename to class name
     * 
     * @param string $filename Migration filename
     * @return string Class name
     */
    private function filenameToClassName(string $filename): string
    {
        // Remove timestamp prefix and .php extension
        $name = preg_replace('/^\d{14}_/', '', $filename);
        $name = str_replace('.php', '', $name);
        
        // Convert snake_case to PascalCase
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        return $name;
    }
}

// CLI interface
if (php_sapi_name() === 'cli') {
    $manager = new MigrationManager();
    
    $command = $argv[1] ?? 'help';

    switch ($command) {
        case 'up':
            $manager->up();
            break;
        case 'down':
            $manager->down();
            break;
        case 'status':
            $manager->status();
            break;
        default:
            echo "Database Migration Tool\n";
            echo str_repeat('=', 40) . "\n";
            echo "Usage: php migrate.php [command]\n\n";
            echo "Commands:\n";
            echo "  up      - Run all pending migrations\n";
            echo "  down    - Rollback last migration\n";
            echo "  status  - Show migration status\n";
            echo "  help    - Show this help\n";
            break;
    }
}
