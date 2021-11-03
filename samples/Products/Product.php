<?php

namespace Osm\Admin\Samples\Products;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Scopes\Attributes\Scoped;
use Osm\Admin\Tables\Attributes\Table;
use Osm\Admin\Tables\Traits\Id;
use Osm\Admin\Tables\Traits\Type;

/**
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
#[Table('products'), Scoped]
class Product extends Object_
{
    use Id, Type;
}