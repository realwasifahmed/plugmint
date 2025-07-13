<?php

namespace Plugmint\Database;

class Schema
{
    public static function create($table, $callback)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tableName = $prefix . $table;
        $blueprint = new Blueprint($table);

        // Call user callback to add columns
        $callback($blueprint);

        $columnsSql = implode(",\n  ", $blueprint->columns);
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
  $columnsSql
) $charsetCollate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        echo "Migrated: $tableName\n";
    }

    public static function drop($table)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $tableName = $prefix . $table;

        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        $wpdb->query($sql);

        echo "Dropped table: $tableName\n";
    }
}
