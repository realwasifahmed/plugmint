<?php

namespace Plugmint\Console\Commands;

class AdminPageCommand
{
    public static function handle($args = [])
    {
        $name = $args[0] ?? null;
        if (!$name) {
            echo "Usage: php mintman make:admin_page MenuName\n";
            return;
        }

        // Setup names
        $className = preg_replace('/[^A-Za-z0-9_]/', '', $name) . 'Controller';
        $slug = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $viewFile = strtolower($slug) . '.php';
        $viewDir = __DIR__ . '/../../views/Admin/';
        $viewPath = $viewDir . $viewFile;

        // Controller
        $controllerDir = __DIR__ . '/../../Controllers/';
        $file = $controllerDir . $className . '.php';

        // Ensure dirs exist
        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0777, true);
        }
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0777, true);
        }

        // Check for collisions
        if (file_exists($file)) {
            echo "Admin page controller already exists: $file\n";
            return;
        }
        if (file_exists($viewPath)) {
            echo "Admin view already exists: $viewPath\n";
            return;
        }

        // Controller template
        $template = <<<EOD
            <?php

            namespace Plugmint\Controllers;

            class $className
            {
                public static function boot()
                {
                    add_action('admin_menu', [self::class, 'register_menu']);
                }

                public static function register_menu()
                {
                    add_menu_page(
                        '$name',
                        '$name',
                        'manage_options',
                        '$slug',
                        [self::class, 'render'],
                        'dashicons-admin-generic'
                    );
                }

                public static function render()
                {
                    // Pass data as needed
                    view('Admin/$slug', [
                        'title' => '$name',
                        'user' => wp_get_current_user()
                    ]);
                }
            }
            EOD;

                    // View template
                    $viewTpl = <<<EOD
            <div class="wrap">
                <h1><?= esc_html(\$title ?? 'Admin Page') ?></h1>
                <p>Hello, <?= esc_html(\$user->display_name ?? 'friend') ?>! 👋 This is the <?= esc_html(\$title) ?> view.</p>
            </div>
            EOD;

        // Write files
        file_put_contents($file, $template);
        file_put_contents($viewPath, $viewTpl);

        echo "Admin page controller created: $file\n";
        echo "Admin page view created: $viewPath\n";
    }
}
