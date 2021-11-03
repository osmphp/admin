<?php

namespace Osm\Admin\Tables\Attributes;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Schema\Property;
use Osm\Admin\Tables\Column as TableColumn;

abstract class Column
{
    protected function getHandlerClassName(): string {
        return str_replace('\\Attributes\\', '\\', static::class);
    }

    public function createHandler(Property $property): TableColumn {
        $new = "{$this->getHandlerClassName()}::new";
        return $new(array_merge(['property' => $property], (array)$this));
    }
}