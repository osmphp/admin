<?php

namespace Osm\Admin\Schema\Property;

use Osm\Admin\Schema\Class_;
use Osm\Core\Attributes\Type;

#[Type('object')]
class Object_ extends Bag
{
    public string $refs_name = Class_::SCHEMA_PROPERTY;
    public string $refs_root_class_name = Class_::ROOT_CLASS_NAME;

}