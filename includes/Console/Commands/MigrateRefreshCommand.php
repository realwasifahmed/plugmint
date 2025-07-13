<?php

namespace Plugmint\Console\Commands;

use Plugmint\Database\MigrationManager;

class MigrateRefreshCommand
{
    public static function handle($args = [])
    {
        echo "Refreshing migrations...\n";
        MigrationManager::refreshAll();
    }
}
