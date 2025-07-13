<?php

namespace Plugmint\Console\Commands;

class ControllerCommand
{
    public static function handle($args = [])
    {
        // 1. Get the controller name from args
        $name = $args[0] ?? null;
        if (!$name) {
            echo "Usage: php mintman make:controller ControllerName\n";
            return;
        }

        // 2. Sanitize and format the class name
        $className = preg_replace('/[^A-Za-z0-9_]/', '', $name);

        // 3. Calculate file and dir paths
        $controllerDir = __DIR__ . '/../../Controllers/';
        $file = $controllerDir . $className . '.php';

        // 4. Create Controllers directory if missing
        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0777, true);
        }

        // 5. Check if the controller already exists
        if (file_exists($file)) {
            echo "Controller already exists: $file\n";
            return;
        }

        // 6. Controller class template
        $template = <<<EOD
<?php

namespace Plugmint\Controllers;

class $className
{

    public static function boot()
    {
        
    }

    public static function register_menu()
    {
      
    }

    public static function render()
    {
      
    }
}
EOD;

        // 7. Write the controller file
        file_put_contents($file, $template);
        echo "Controller created: $file\n";
    }
}
