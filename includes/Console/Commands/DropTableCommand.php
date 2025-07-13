<?php

namespace Plugmint\Console\Commands;

use Plugmint\Database\Schema;
use Plugmint\Database\MigrationManager;

class DropTableCommand extends MigrationManager
{
    public static function handle($args = [])
    {
        $table = $args[0] ?? null;
        if (!$table) {
            echo "Usage: php mintman drop:table table_name\n";
            return;
        }

        // 1. Drop the actual table
        Schema::drop($table);

        // 2. Delete any migration records that mention this table
        self::deleteMigrationRecords($table);
    }

    /**
     * Removes migration records from plugmint_migrations for the given table.
     */
    protected static function deleteMigrationRecords($table)
    {
        global $wpdb;
        $migrationsTable = $wpdb->prefix . MigrationManager::$table;

        // Look for migrations that created this table (by name in migration file)
        // This logic assumes you use "create_{$table}_table" pattern in migration filenames
        $pattern = 'create_' . strtolower($table) . '_table';

        // Find matching migrations
        $sql = $wpdb->prepare("SELECT migration FROM `$migrationsTable` WHERE migration LIKE %s", '%' . $wpdb->esc_like($pattern) . '%');
        $migrations = $wpdb->get_col($sql);

        if ($migrations) {
            foreach ($migrations as $migration) {
                $wpdb->delete($migrationsTable, ['migration' => $migration]);
                echo "Deleted migration record: $migration\n";
            }
        } else {
            echo "No migration records found for table '$table'.\n";
        }
    }
}
