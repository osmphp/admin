<?php

namespace Osm\Data\Samples\Configurables;

use Osm\Data\Base\Attributes\Type;
use Osm\Data\Samples\Products\Product;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[] $axes #[Serialized]
 */
#[Type('configurable')]
class Configurable extends Product
{

}