<?php

namespace Osm\Admin\Samples\Configurables;

use Osm\Admin\Base\Attributes\Type;
use Osm\Admin\Samples\Products\Product;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[] $axes #[Serialized]
 */
#[Type('configurable')]
class Configurable extends Product
{

}