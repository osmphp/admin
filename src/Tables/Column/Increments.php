<?php

namespace Osm\Admin\Tables\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Tables\Column;
use Osm\Core\Attributes\Type;

#[Type('increments')]
class Increments extends Column
{
    public function create(Blueprint $table): void {
        $table->increments($this->property->name);
    }
}