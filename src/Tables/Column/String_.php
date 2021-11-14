<?php

namespace Osm\Admin\Tables\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Tables\Column;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;

/**
 */
#[Type('string')]
class String_ extends Column
{
    public function create(Blueprint $table): void
    {
        $column = $table->string($this->property->name);

        if ($this->property->nullable) {
            $column->nullable();
        }
    }
}