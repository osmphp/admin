<?php

namespace Osm\Data\Samples\Products;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Scopes\Attributes\Scoped;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Traits\Id;
use Osm\Data\Tables\Traits\Type;

/**
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
#[Table('products'), Scoped]
class Product extends Object_
{
    use Id, Type;
}