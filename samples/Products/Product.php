<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Base\Attributes\Icon;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Scoped;
use Osm\Admin\Base\Attributes\Table;
use Osm\Admin\Base\Traits\Id;
use Osm\Admin\Base\Traits\Type;

/**
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
#[
    Table('products'),
    Scoped,
    Icon('/products/', 'Products'),
]
class Product extends Object_
{
    use Id, Type;
}