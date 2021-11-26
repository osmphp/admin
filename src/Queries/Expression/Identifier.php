<?php

namespace Osm\Admin\Queries\Expression;

use Osm\Admin\Queries\Expression;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Type;

/**
 * @property Property[] $properties
 */
#[Type('identifier')]
class Identifier extends Expression
{

}