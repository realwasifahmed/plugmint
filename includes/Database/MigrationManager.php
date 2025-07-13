<?php

namespace Plugmint\Database;

class MigrationManager
{
    protected static $table = 'plugmint_migrations';

    protected static function ensureMigrationsTable()
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table;
        $charsetCollate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL DEFAULT 1,
            `ran_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`migration`)
        ) $charsetCollate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    protected static function ranMigrations()
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table;
        $migrations = $wpdb->get_col("SELECT migration FROM `$table`");
        return $migrations ?: [];
    }

    protected static function markAsRan($migration)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table;
        $wpdb->insert($table, [
            'migration' => $migration,
            'batch'     => 1,
        ]);
    }

    public static function runAll()
    {
        self::ensureMigrationsTable();

        $dir = __DIR__ . '/migrations/';
        $files = glob($dir . '*.php');

        if (!$files) {
            echo "No migration files found.\n";
            return;
        }

        $ran = self::ranMigrations();

        foreach ($files as $file) {
            require_once $file;

            $base = basename($file, '.php');
            // Split into parts: 2025_07_13_015400_Test_Migration
            $parts = explode('_', $base, 5);
            // The last part (5th) is the class part (with underscores for multi-word)
            $classPart = isset($parts[4]) ? $parts[4] : $parts[count($parts) - 1];
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $classPart)));
            $fqcn = "Plugmint\\Database\\Migrations\\$className";

            // Use just the filename for migration tracking
            $migrationName = $base;

            if (in_array($migrationName, $ran)) {
                echo "Already migrated: $migrationName\n";
                continue;
            }

            if (class_exists($fqcn) && method_exists($fqcn, 'up')) {
                echo "Migrating: $className ...\n";
                $fqcn::up();
                self::markAsRan($migrationName);
                echo "Migrated: $migrationName\n";
            } else {
                echo "Migration class/method not found: $fqcn\n";
            }
        }
    }

    public static function refreshAll()
    {
        self::ensureMigrationsTable();

        $dir = __DIR__ . '/migrations/';
        $files = glob($dir . '*.php');
        if (!$files) {
            echo "No migration files found.\n";
            return;
        }

        // 1. Run down() on each migration (drop tables, if down() exists)
        foreach ($files as $file) {
            require_once $file;

            // Extract class name (same as runAll logic)
            $base = basename($file, '.php');
            $parts = explode('_', $base, 5);
            $classPart = isset($parts[4]) ? $parts[4] : $parts[count($parts) - 1];
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $classPart)));
            $fqcn = "Plugmint\\Database\\Migrations\\$className";

            if (class_exists($fqcn) && method_exists($fqcn, 'down')) {
                echo "Rolling back: $className ...\n";
                $fqcn::down();
            }
        }

        // 2. Empty the migrations table
        global $wpdb;
        $migrationsTable = $wpdb->prefix . self::$table;
        $wpdb->query("TRUNCATE TABLE `$migrationsTable`");
        echo "Migration history cleared.\n";

        // 3. Re-run all migrations (up)
        self::runAll();
    }
}
