<?php

namespace Osm\Data\Schema;

use Osm\Core\Class_ as CoreClass;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $name #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string $type_class_names #[Serialized]
 * @property Class_[] $types
 * @property CoreClass $reflection
 */
class Class_ extends Object_
{

}