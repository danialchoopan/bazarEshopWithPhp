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

// Load environment variables and core classes
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/ErrorHandler.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/CouponManager.php';
require_once __DIR__ . '/../core/ZarinPalPayment.php';

use BazarShop\Core\Database;

class MigrationManager
{
    private const TABLE = 'migrations';
    private string $migrationsDir;
    private Database $db;

    public function __construct()
    {
        $this->migrationsDir = __DIR__ . '/migrations';
        $this->db = new Database();
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable(): void
    {
        $tableName = self::TABLE;
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $this->db->getConnection()->exec($sql);
        } catch (\Exception $e) {
            echo "Note: Could not create migrations table: " . $e->getMessage() . "\n";
        }
    }

    private function getExecutedMigrations(): array
    {
        try {
            $tableName = self::TABLE;
            $stmt = $this->db->getConnection()->query("SELECT migration FROM $tableName ORDER BY id");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsDir)) {
            return [];
        }

        $files = scandir($this->migrationsDir);
        $files = array_diff($files, ['.', '..']);
        $files = array_filter($files, fn($f) => str_ends_with($f, '.php'));
        sort($files);

        return $files;
    }

    public function up(): void
    {
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();
        $pending = array_diff($files, $executed);

        if (empty($pending)) {
            echo "✓ No pending migrations.\n";
            $this->runSeeders();
            return;
        }

        echo "Running " . count($pending) . " migration(s)...\n\n";

        foreach ($pending as $file) {
            echo "→ Running: {$file}\n";
            
            try {
                require_once $this->migrationsDir . '/' . $file;
                $className = $this->filenameToClassName($file);
                
                if (!class_exists($className)) {
                    throw new \Exception("Migration class '{$className}' not found in {$file}");
                }

                $migration = new $className($this->db);
                $migration->up();

                $tableName = self::TABLE;
                $stmt = $this->db->getConnection()->prepare("INSERT INTO $tableName (migration) VALUES (?)");
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
        $this->runSeeders();
    }
    
    private function runSeeders(): void
    {
        $seedData = getenv('SEED_DATA') ?: 'true';
        
        if (filter_var($seedData, FILTER_VALIDATE_BOOLEAN)) {
            echo "\n🌱 SEED_DATA is enabled. Running seeders...\n";
            
            $seederFile = __DIR__ . '/seeder.php';
            if (file_exists($seederFile)) {
                try {
                    require_once $seederFile;
                    $seeder = new DatabaseSeeder($this->db);
                    $seeder->run();
                    echo "✓ Seeding completed successfully.\n";
                } catch (\Exception $e) {
                    echo "✗ Seeding failed: " . $e->getMessage() . "\n";
                }
            } else {
                echo "⚠ Seeder file not found. Skipping seeding.\n";
            }
        } else {
            echo "\n⊘ SEED_DATA is disabled. Skipping seeding.\n";
        }
    }

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

            $tableName = self::TABLE;
            $stmt = $this->db->getConnection()->prepare("DELETE FROM $tableName WHERE migration = ?");
            $stmt->execute([$lastMigration]);

            echo "✓ Rolled back: {$lastMigration}\n";
        } catch (\Exception $e) {
            echo "✗ Failed to rollback: {$lastMigration}\n";
            echo "  Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

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

    private function filenameToClassName(string $filename): string
    {
        $name = preg_replace('/^\d{14}_/', '', $filename);
        $name = str_replace('.php', '', $name);
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        return $name;
    }
}

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
