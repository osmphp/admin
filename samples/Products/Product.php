<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Base\Attributes\Icon;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Storage;
use Osm\Admin\Base\Traits\Id;
use Osm\Admin\Base\Traits\SubTypes;

/**
 * @property string $sku #[Serialized]
 * @property string $description #[Serialized]
 */
#[
    Storage\ScopedTable('products'),
    Icon('/products/', 'Products'),
]
class Product extends Object_
{
    use Id, SubTypes;
}