<?php

namespace Osm\Admin\Schema\Property;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Property as CoreProperty;
use Osm\Admin\Base\Attributes\Type;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
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