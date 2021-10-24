<?php

namespace Osm\Data\Samples\Configurables;

use Osm\Core\Attributes\Name;
use Osm\Data\Samples\Products\Product;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[] $axes #[Serialized]
 */
#[Name('configurable')]
class Configurable extends Product
{

}