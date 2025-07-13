<?php

namespace Plugmint\Console;

use Plugmint\Console\Commands\AdminPageCommand;
use Plugmint\Console\Commands\ControllerCommand;
use Plugmint\Console\Commands\DropTableCommand;
use Plugmint\Console\Commands\HelloCommand;
use Plugmint\Console\Commands\MakeModelCommand;
use Plugmint\Console\Commands\MigrateRefreshCommand;
use Plugmint\Console\Commands\MigrationCommand;
use Plugmint\Console\Commands\SubmenuCommand;
use Plugmint\Database\MigrationManager;



class Kernel
{
    protected static $commands = [
        'migrate' => [MigrationManager::class, 'runAll'],
        'hello' => [HelloCommand::class, 'handle'],
        'make:controller' => [ControllerCommand::class, 'handle'],
        'make:migration' => [MigrationCommand::class, 'handle'],
        'make:admin_menu' => [AdminPageCommand::class, 'handle'],
        'make:submenu' => [SubmenuCommand::class, 'handle'],
        'drop:table' => [DropTableCommand::class, 'handle'],
        'migrate:refresh' => [MigrateRefreshCommand::class, 'handle'],
        'make:model' => [MakeModelCommand::class, 'handle'],
    ];

    public static function handle($argv)
    {
        $cmd = $argv[1] ?? null;
        if (!$cmd || !isset(self::$commands[$cmd])) {
            self::printHelp();
            exit(1);
        }
        $args = array_slice($argv, 2);
        call_user_func(self::$commands[$cmd], $args);
    }

    protected static function printHelp()
    {
        echo "Plugmint Console (Artisan-Style)\n";
        echo "Usage: php bin/plugmint [command]\n";
        echo "Available commands:\n";
        foreach (array_keys(self::$commands) as $command) {
            echo "  $command\n";
        }
    }
}
