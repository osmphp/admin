<?php

namespace Osm\Admin\Samples\Products;

use Carbon\Carbon;
use Osm\Admin\Base\Record;
use Osm\Admin\Base\Attributes\Default_;

/**
 * @property ?string $name
 * @property int $qty #[Default_(0)]
 * @property float $price #[Default_(0.0)]
 * @property bool $enabled #[Default_(true)]
 * @property Carbon $created_at
 */
class Product extends Record
{

}