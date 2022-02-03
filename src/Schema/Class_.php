<?php

namespace Osm\Admin\Schema;

use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Schema $schema
 * @property CoreClass $reflection
 * @property string $name #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string[] $type_class_names #[Serialized]
 * @property Class_\Type[] $types
 * @property Object_ $instance
 */
class Class_ extends Object_
{
    use SubTypes;

    protected function get_schema(): Schema {
        throw new NotImplemented($this);
    }

    protected function get_reflection(): CoreClass {
        throw new NotImplemented($this);
    }

    protected function get_name(): string {
        throw new NotImplemented($this);
    }

    protected function get_properties(): array {
        throw new NotImplemented($this);
    }

    protected function get_type_class_names(): array {
        throw new NotImplemented($this);
    }

    protected function get_types(): array {
        throw new NotImplemented($this);
    }

    protected function get_instance(): Object_ {
        throw new NotImplemented($this);
    }

}