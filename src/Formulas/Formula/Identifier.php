<?php

namespace Osm\Admin\Formulas\Formula;

use Osm\Admin\Formulas\Formula;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Type;

/**
 * @property Property[] $accessors
 * @property Property $property
 * @property bool $wildcard
 */
#[Type('identifier')]
class Identifier extends Formula
{

}