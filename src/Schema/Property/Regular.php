<?php

namespace Osm\Data\Schema\Property;

use Osm\Core\Property as CoreProperty;
use Osm\Data\Base\Attributes\Type;
use Osm\Data\Schema\Property;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $module_class_name #[Serialized]
 * @property CoreProperty $reflection
 */
#[Type('regular')]
class Regular extends Property
{
    protected function get_reflection(): CoreProperty {
        return $this->class->reflection->properties[$this->name];
    }
}