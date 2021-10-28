<?php

namespace Osm\Data\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;
use Osm\Data\Migrations\Migration;
use Osm\Data\Schema\Class_;

/**
 * @property Class_ $class
 */
class CreateTable extends Migration
{
    public function run(): void
    {
        $this->db->create($this->class->table, function(Blueprint $table) {
            $table->increments('id');
            $table->json('data')->nullable();
        });
    }
}