<?php

namespace Osm\Data\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Migrations\Migration;
use Osm\Data\Schema\Property;
use Osm\Data\Tables\Attributes\Column;

class Table extends Migration
{
    protected function createColumn(Blueprint $table, Property $property,
        string $prefix): void
    {
        $this->{"createColumn_" . $property->column->getMethod()}($table,
            $property, $prefix);
    }

    protected function createColumn_increments(Blueprint $table,
        Property $property, string $prefix): void
    {
        $table->increments("{$prefix}{$property->name}");
    }

    protected function createColumn_int_(Blueprint $table,
        Property $property, string $prefix): void
    {
        /* @var Column\Int_ $column */
        $column = $property->column;
        $fluent = $table->integer("{$prefix}{$property->name}");

        if ($column->unsigned) {
            $fluent->unsigned();
        }

        if ($column->references) {
            list($foreignTable, $foreignColumn) =
                explode('.', $column->references);

            $fluent = $table->foreign("{$prefix}{$property->name}")
                ->references($foreignColumn)->on($foreignTable);

            if ($column->on_delete) {
                $fluent->onDelete($column->on_delete);
            }
        }
    }
}