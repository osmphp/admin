<?php

namespace Osm\Data\Samples\Products;

use Osm\Core\Object_;
use Osm\Data\Base\Traits\Types;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Scopes\Attributes\Scoped;
use Osm\Data\Tables\Attributes\Table;

/**
 * @property int $id #[Serialized]
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
#[Table('products'), Scoped]
class Product extends Object_
{
    use Types;
}