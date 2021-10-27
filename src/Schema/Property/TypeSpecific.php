<?php

namespace Osm\Data\Schema\Property;

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

}