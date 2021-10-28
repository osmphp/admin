<?php

namespace Osm\Data\Schema\Property;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Property as CoreProperty;
use Osm\Data\Base\Attributes\Type;
use Osm\Data\Schema\Class_;
use Osm\Data\Schema\Property;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[]|null $type_names #[Serialized] Subtype names defining
 *      this property
 * @property Class_[] $types Subtypes defining this property
 */
#[Type('type_specific')]
class TypeSpecific extends Property
{
    protected function get_reflection(): CoreProperty {
        foreach ($this->types as $type) {
            return $type->reflection->properties[$this->name];
        }

        // when a typed property doesn't know about types that define it
        throw new NotImplemented($this);
    }

}