<?php

namespace Plugmint\Console\Commands;

class MakeModelCommand
{
    public static function handle($args = [])
    {
        $name = $args[0] ?? null;

        if (!$name) {
            echo "Usage: php mintman make:model ModelName\n";
            return;
        }

        $className = self::toClassName($name);
        $fileName = $className . '.php';
        $modelDir = __DIR__ . '/../../Models/';
        $file = $modelDir . $fileName;

        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0777, true);
        }

        // DOUBLE-CHECK: Don't overwrite
        if (file_exists($file)) {
            echo "Model already exists: $file\n";
            return;
        }

        $tableName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className)) . 's';

        $template = <<<EOD
<?php

namespace Plugmint\Models;

class $className extends BaseModel
{
    protected \$table = '$tableName';

    // Define relationships and custom methods here

    // Example:
    // public function orders() {
    //     return \$this->hasMany(Order::class, 'user_id');
    // }
}
EOD;

        file_put_contents($file, $template);
        echo "Model created: $file\n";
    }

    private static function toClassName($name)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
}
