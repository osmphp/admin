<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Schema\Attributes\Faceted;
use Osm\Admin\Schema\Attributes\Option;
use Osm\Admin\Schema\Record;

/**
 * @property string $color #[Option(Color::class), Faceted]
 *
 * @uses Option, Faceted, Color
 */
class Product extends Record
{
}