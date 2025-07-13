<?php

namespace Plugmint\Database\Migrations;

use Plugmint\Database\Schema;

class CreateOrdersTable
{
    public static function up()
    {
        Schema::create('orders', function($table) {
            $table->id();
            $table->string('column1');
            $table->text('Data');
            $table->timestamps();
        });
    }

    public static function down()
    {
        Schema::drop('orders');
    }
}