<?php

namespace Plugmint\Models;

abstract class BaseModel
{
    protected static $table;
    protected static $primaryKey = 'id';
    protected $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    // Static: Find by PK
    public static function find($id)
    {
        global $wpdb;
        $table = static::$table ?? static::tableName();
        $primary = static::$primaryKey;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM `$table` WHERE `$primary` = %d LIMIT 1", $id),
            ARRAY_A
        );
        return $row ? new static($row) : null;
    }

    // Static: Get all records
    public static function all()
    {
        global $wpdb;
        $table = static::$table ?? static::tableName();
        $results = $wpdb->get_results("SELECT * FROM `$table`", ARRAY_A);
        return array_map(fn($row) => new static($row), $results);
    }

    // Static: Where query (simple, for demo)
    public static function where($column, $value)
    {
        global $wpdb;
        $table = static::$table ?? static::tableName();
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM `$table` WHERE `$column` = %s", $value),
            ARRAY_A
        );
        return array_map(fn($row) => new static($row), $results);
    }

    // Static: Create
    public static function create($data)
    {
        global $wpdb;
        $table = static::$table ?? static::tableName();
        $wpdb->insert($table, $data);
        $id = $wpdb->insert_id;
        return static::find($id);
    }

    // Instance: Save (update)
    public function save()
    {
        global $wpdb;
        $table = static::$table ?? static::tableName();
        $primary = static::$primaryKey;
        if (!empty($this->attributes[$primary])) {
            // Update
            $id = $this->attributes[$primary];
            $data = $this->attributes;
            unset($data[$primary]);
            $wpdb->update($table, $data, [$primary => $id]);
        } else {
            // Insert
            $wpdb->insert($table, $this->attributes);
            $this->attributes[$primary] = $wpdb->insert_id;
        }
        return $this;
    }

    // Instance: Delete
    public function delete()
    {
        global $wpdb;
        $table = static::$table ?? static::tableName();
        $primary = static::$primaryKey;
        if (!empty($this->attributes[$primary])) {
            $wpdb->delete($table, [$primary => $this->attributes[$primary]]);
        }
    }

    // Magic getter/setter
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    // Helper to guess table name if not set
    protected static function tableName()
    {
        // e.g., Order -> orders, BlogPost -> blog_posts
        $called = get_called_class();
        $short = strtolower(preg_replace('/^.*\\\\/', '', $called));
        return (substr($short, -1) === 's') ? $short : $short . 's';
    }

    public function hasMany($relatedClass, $foreignKey, $localKey = null)
    {
        global $wpdb;
        $localKey = $localKey ?: $this->primaryKey;
        $localValue = $this->attributes[$localKey] ?? null;

        if (!$localValue) return [];

        $related = new $relatedClass();
        $relatedTable = $related->table;
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}{$relatedTable} WHERE {$foreignKey} = %s", $localValue),
            ARRAY_A
        );

        return array_map(function ($row) use ($relatedClass) {
            return new $relatedClass($row);
        }, $results);
    }

    public function belongsTo($relatedClass, $foreignKey, $ownerKey = 'id')
    {
        global $wpdb;
        $foreignValue = $this->attributes[$foreignKey] ?? null;
        if (!$foreignValue) return null;

        $related = new $relatedClass();
        $relatedTable = $related->table;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}{$relatedTable} WHERE {$ownerKey} = %s", $foreignValue),
            ARRAY_A
        );

        return $row ? new $relatedClass($row) : null;
    }
}
