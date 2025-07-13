<?php

namespace Plugmint\Database;

class Blueprint
{
    public $columns = [];
    public $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function id($name = 'id')
    {
        $this->columns[] = "`$name` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string($name, $length = 255)
    {
        $this->columns[] = "`$name` VARCHAR($length) NOT NULL";
        return $this;
    }

    public function text($name)
    {
        $this->columns[] = "`$name` TEXT NOT NULL";
        return $this;
    }

    public function integer($name)
    {
        $this->columns[] = "`$name` INT NOT NULL";
        return $this;
    }

    public function boolean($name)
    {
        $this->columns[] = "`$name` TINYINT(1) NOT NULL";
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = "`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }
}
