<?php

namespace Osm\Admin\Samples\Configurables;

use Osm\Admin\Samples\Products\Product;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;

/**
 * @property string[] $axes #[Serialized]
 */
#[Type('configurable')]
class Configurable extends Product
{

}