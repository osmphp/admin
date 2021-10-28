<?php

namespace Osm\Data\Samples\Products;

use Osm\Core\Attributes\Name;
use Osm\Data\Queries\Attributes\Of;
use Osm\Data\Scopes\Query;

#[Name('products'), Of(Product::class)]
class Products extends Query
{

}