<?php

namespace Osm\Data\Schema;

use Osm\Core\Class_ as CoreClass;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Schema $schema
 * @property string $name #[Serialized]
 * @property string $module_class_name #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string $type_class_names #[Serialized]
 * @property Class_[] $types
 * @property CoreClass $reflection
 */
class Class_ extends Object_
{
    protected function get_module_class_name(): string {
        return $this->reflection->module_class_name;
    }
}