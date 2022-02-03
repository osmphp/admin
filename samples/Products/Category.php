<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Schema\Record;

/**
 * @property string $name
 * @property Category $parent
 * @property Category[] $children
 */
class Category extends Record
{

}