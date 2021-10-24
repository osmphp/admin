<?php

namespace Osm\Data\Samples\Inventory\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\UseIn;
use Osm\Data\Samples\Products\Product;

/**
 * @property bool $in_stock #[Serialized]
 */
#[UseIn(Product::class)]
trait ProductTrait
{

}