<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Schema\Attributes\Faceted;
use Osm\Admin\Schema\Attributes\Option;
use Osm\Admin\Schema\Record;

/**
 * @property string $color #[Option(Color::class), Faceted]
 *
 * @uses Option, Faceted
 */
class Product extends Record
{
/*
 * @property ?string $name
 * @property ?string $type
 * @property int $qty #[Default_(0)]
 * @property float $price #[Default_(0.0)]
 * @property bool $enabled #[Default_(true)]
 * @property Carbon $created_at
 *
 * @uses Default_
 */
}