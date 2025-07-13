<?php
global $argv;

use Plugmint\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Helpers/helpers.php';

// Load all controllers and boot them
$controllerDir = __DIR__ . '/Controllers/';
foreach (glob($controllerDir . '*.php') as $file) {
    require_once $file;
    $class = 'Plugmint\\Controllers\\' . basename($file, '.php');
    if (class_exists($class) && method_exists($class, 'boot')) {
        $class::boot();
    }
}

// (Optional: If your commands use WP stuff, load WordPress here)
// require_once '/path/to/your/wp-load.php';
if (php_sapi_name() !== 'cli') {
    return;
}
Kernel::handle($argv);
