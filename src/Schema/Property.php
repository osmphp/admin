<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Property as CoreProperty;
use Osm\Admin\Base\Traits\SubTypes;

/**
 * @property string $name #[Serialized]
 * @property string $class_name #[Serialized]
 * @property Class_ $class
 * @property CoreProperty $reflection
 * @property bool $nullable #[Serialized]
 */
class Property extends Object_
{
    use SubTypes;

    protected function get_reflection(): CoreProperty {
        throw new NotImplemented($this);
    }

    protected function get_nullable(): bool {
        return $this->reflection->nullable;
    }
}