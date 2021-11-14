<?php

namespace Osm\Admin\Tables\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Tables\Column;
use Osm\Admin\Tables\Traits\ForeignKey;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;

/**
 * @property bool $unsigned #[Serialized]
 */
#[Type('int')]
class Int_ extends Column
{
    use ForeignKey;

    public function create(Blueprint $table): void
    {
        $column = $table->integer($this->property->name);

        if ($this->unsigned) {
            $column->unsigned();
        }

        if ($this->property->nullable) {
            $column->nullable();
        }

        $this->createForeignKey($table);
    }
}