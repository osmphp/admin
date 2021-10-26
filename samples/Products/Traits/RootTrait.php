<?php

namespace Osm\Data\Samples\Products\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Data\Base\Root;
use Osm\Data\Samples\Products\Product;

/**
 * @property Product[] $products
 */
#[UseIn(Root::class)]
trait RootTrait
{

}