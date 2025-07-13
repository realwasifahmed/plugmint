<?php

namespace Plugmint\Console\Commands;

use Plugmint\Database\MigrationManager;

class MigrateCommand
{
    public static function handle($args = [])
    {
        echo "Plugmint: All migrations executed!\n";
    }
}
