<?php

namespace Osm\Admin\Schema\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

#[Type('datetime')]
class DateTime extends Scalar
{
    public function create(Blueprint $table): void {
        $column = $table->dateTime($this->name);

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}