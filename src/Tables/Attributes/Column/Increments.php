<?php

namespace Osm\Data\Tables\Attributes\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Schema\Property;
use Osm\Data\Tables\Attributes\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Increments extends Column
{
    public function create(Blueprint $table, Property $property,
        string $prefix): void
    {
        $table->increments("{$prefix}{$property->name}");
    }

    public function createKey(Blueprint $table, Property $property): void
    {
        $table->increments($property->name);
    }

    public function createScope(Blueprint $table, Property $property,
        string $prefix): void
    {
        $table->unsignedInteger("{$prefix}{$property->name}")->primary();
        $table->foreign("{$prefix}{$property->name}")
            ->references($property->name)
            ->on($property->class->table)
            ->onDelete('cascade');
    }
}