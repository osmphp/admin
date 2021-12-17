<?php

namespace Osm\Admin\Interfaces;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Interface_ $interface
 * @property string $name #[Serialized]
 */
class Parameter extends Object_
{
    use SubTypes;
}