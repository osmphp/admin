<?php

namespace Osm\Admin\Schema\Diff\Property;

use Osm\Core\Attributes\Type;
use Osm\Admin\Schema\Property\Int_ as IntPropertyObject;

/**
 * @property \stdClass|IntPropertyObject|null $old
 * @property IntPropertyObject $new
 */
#[Type('int')]
class Int_ extends Scalar {
}