<?php

namespace Osm\Data\Samples\Products;

use Osm\Core\Object_;
use Osm\Data\Base\Traits\Types;
use Osm\Core\Attributes\Serialized;

/**
 * @property int $id #[Serialized]
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
class Product extends Object_
{
    use Types;
}