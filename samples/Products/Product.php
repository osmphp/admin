<?php

namespace Osm\Data\Samples\Products;

use Osm\Data\Base\Traits\Subtypes;
use Osm\Data\Tables\Record;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
class Product extends Record
{
    use Subtypes;
}