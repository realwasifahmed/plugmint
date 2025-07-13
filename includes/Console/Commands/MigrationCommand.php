<?php

namespace Plugmint\Console\Commands;

class MigrationCommand
{
    public static function handle($args = [])
    {
        $name = $args[0] ?? null;

        if (!$name) {
            echo "Usage: php mintman make:migration migration_name\n";
            return;
        } else {
            // Format name for file/class
            $migrationName = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
            $className = self::toClassName($migrationName);
            $timestamp = date('Y_m_d_His');
            $filename = "{$timestamp}_{$migrationName}.php";

            // Table name (snake_case from migration name)
            $table = strtolower(preg_replace('/^create_|_table$/i', '', $migrationName));
            if (!$table) $table = 'your_table';

            // Paths
            $migrationDir = __DIR__ . '/../../Database/migrations/';
            $file = $migrationDir . $filename;

            // Ensure directory exists
            if (!is_dir($migrationDir)) {
                mkdir($migrationDir, 0777, true);
            }

            // DOUBLE-CHECK: Does a migration with this name already exist?
            $searchPattern = $migrationDir . '*_' . $migrationName . '.php';
            $existingFiles = glob($searchPattern);
            if ($existingFiles && count($existingFiles) > 0) {
                echo "Migration with this name already exists: " . $existingFiles[0] . "\n";
                return;
            }

            // File template with Schema Builder
            $template = <<<EOD
<?php

namespace Plugmint\Database\Migrations;

use Plugmint\Database\Schema;

class $className
{
    public static function up()
    {
        Schema::create('$table', function(\$table) {
            \$table->id();
            \$table->string('column1');
            \$table->timestamps();
        });
    }

    public static function down()
    {
        global \$wpdb;
        \$wpdb->query("DROP TABLE IF EXISTS " . \$wpdb->prefix . "'$table'");
    }
}
EOD;

            // Write file
            file_put_contents($file, $template);
            echo "Migration created: $file\n";
        }
    }

    // Helper: Converts migration_name to PascalCase
    private static function toClassName($name)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }
}
