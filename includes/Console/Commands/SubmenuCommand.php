<?php

namespace Plugmint\Console\Commands;

class SubmenuCommand
{
    public static function handle($args = [])
    {
        // Prompt for parent slug if not in $args
        $parent = $args[0] ?? self::ask('Enter parent menu slug (e.g. woogent): ');
        if (!$parent) {
            echo "Parent slug is required.\n";
            return;
        }

        // Prompt for submenu name if not in $args
        $submenu = $args[1] ?? self::ask('Enter submenu name (e.g. Orders): ');
        if (!$submenu) {
            echo "Submenu name is required.\n";
            return;
        }

        // Setup names
        $className = preg_replace('/[^A-Za-z0-9_]/', '', $submenu) . 'Controller';
        $slug = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $submenu));
        $viewFile = strtolower($slug) . '.php';
        $viewDir = __DIR__ . '/../../views/Admin/';
        $viewPath = $viewDir . $viewFile;

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
            echo "Submenu controller already exists: $file\n";
            return;
        }
        if (file_exists($viewPath)) {
            echo "Submenu view already exists: $viewPath\n";
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
                add_action('admin_menu', [self::class, 'register_submenu']);
            }

            public static function register_submenu()
            {
                add_submenu_page(
                    '$parent',                 // Parent slug
                    '$submenu',                // Page title
                    '$submenu',                // Menu title
                    'manage_options',          // Capability
                    '{$parent}_$slug',         // Submenu slug
                    [self::class, 'render']    // Callback
                );
            }

            public static function render()
            {
                view('Admin/$slug', [
                    'title' => '$submenu',
                    'user' => wp_get_current_user()
                ]);
            }
        }
        EOD;

                // View template
                $viewTpl = <<<EOD
        <div class="wrap">
            <h1><?= esc_html(\$title ?? 'Submenu Page') ?></h1>
            <p>Hello, <?= esc_html(\$user->display_name ?? 'friend') ?>! 👋 This is the <?= esc_html(\$title) ?> submenu view.</p>
        </div>
        EOD;

        // Write files
        file_put_contents($file, $template);
        file_put_contents($viewPath, $viewTpl);

        echo "Submenu controller created: $file\n";
        echo "Submenu view created: $viewPath\n";
    }

    // Helper for interactive CLI prompt
    private static function ask($prompt)
    {
        echo $prompt;
        return trim(fgets(STDIN));
    }
}
