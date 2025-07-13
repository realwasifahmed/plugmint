<?php

namespace Plugmint\Console\Commands;

class MakeSeederCommand
{
    public static function handle($args = [])
    {
        $name = $args[0] ?? null;
        if (!$name) {
            echo "Usage: php mintman make:seeder SeederName\n";
            return;
        }

        $className = self::toClassName($name);
        $fileName = $className . '.php';
        $seederDir = __DIR__ . '/../../Database/seeders/';
        $file = $seederDir . $fileName;

        if (!is_dir($seederDir)) {
            mkdir($seederDir, 0777, true);
        }

        if (file_exists($file)) {
            echo "Seeder already exists: $file\n";
            return;
        }

        $template = <<<EOD
<?php

namespace Plugmint\Database\Seeders;

use Faker\Factory as Faker;

class $className
{
    public static function run()
    {
        global \$wpdb;
        \$faker = Faker::create();

        // Example: Seed users table
        for (\$i = 0; \$i < 10; \$i++) {
            \$wpdb->insert(
                \$wpdb->prefix . 'users',
                [
                    'user_login'   => \$faker->userName,
                    'user_email'   => \$faker->unique()->safeEmail,
                    'user_pass'    => wp_hash_password('password'),
                    'user_status'  => 0,
                ]
            );
        }
        echo "Seeded 10 users!\\n";
    }
}
EOD;

        file_put_contents($file, $template);
        echo "Seeder created: $file\n";
    }

    private static function toClassName($name)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
}
