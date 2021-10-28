<?php

namespace Osm\Data\Tables\Attributes\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Data\Schema\Property;
use Osm\Data\Tables\Attributes\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Int_ extends Column
{
    public function __construct(
        public bool $unsigned = false,
        public ?string $references = null,
        public ?string $on_delete = null,
    )
    {
    }

    public function create(Blueprint $table, Property $property,
        string $prefix): void
    {
        $column = $table->integer("{$prefix}{$property->name}");

        if ($this->unsigned) {
            $column->unsigned();
        }

        if ($this->references) {
            list($foreignTable, $foreignColumn) =
                explode('.', $this->references);

            $foreign = $table->foreign("{$prefix}{$property->name}")
                ->references($foreignColumn)
                ->on($foreignTable);

            if ($this->on_delete) {
                $foreign->onDelete($this->on_delete);
            }
        }
    }
}