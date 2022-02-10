<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Schema\Attributes\Table;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Attributes\Explicit;

/**
 * @property string $name
 * @property ?Category $parent #[Explicit]
 * @property Category[] $children
 *
 * @uses Explicit
 */
#[Table('categories')]
class Category extends Record
{

}