<?php

namespace Osm\Admin\Schema\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Class_;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

#[Type('object')]
class Object_ extends Bag
{
    public string $refs_name = Class_::SCHEMA_PROPERTY;
    public string $refs_root_class_name = Class_::ROOT_CLASS_NAME;

    public function create(Blueprint $table): void {
        $column = $table->json($this->name);

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}