<?php

namespace Osm\Admin\Samples\Products;

use Carbon\Carbon;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Attributes\Default_;

/**
 * @property ?string $name
 * @property ?string $type
 * @property int $qty #[Default_(0)]
 * @property float $price #[Default_(0.0)]
 * @property bool $enabled #[Default_(true)]
 * @property Carbon $created_at
 *
 * @uses Default_
 */
class Product extends Record
{

}