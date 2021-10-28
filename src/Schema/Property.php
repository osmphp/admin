<?php

namespace Osm\Data\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Property as CoreProperty;
use Osm\Data\Base\Traits\Type;

/**
 * @property string $name #[Serialized]
 * @property string $class_name #[Serialized]
 * @property Class_ $class
 * @property CoreProperty $reflection
 */
class Property extends Object_
{
    use Type;

    protected function get_reflection(): CoreProperty {
        throw new NotImplemented($this);
    }
}