<?php

namespace Osm\Data\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Base\Traits\Subtypes;

/**
 * @property string $name #[Serialized]
 * @property string $class_name #[Serialized]
 * @property Class_ $class
 * @property bool $primitive #[Serialized]
 */
class Property extends Object_
{
    use Subtypes;

    protected function get_primitive(): bool {
        throw new NotImplemented($this);
    }
}