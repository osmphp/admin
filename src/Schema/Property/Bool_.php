<?php

namespace Osm\Admin\Schema\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Type;

#[Type('bool')]
class Bool_ extends Scalar
{
    public function create(Blueprint $table): void {
        $column = $table->boolean($this->name);

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}