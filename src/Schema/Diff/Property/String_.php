<?php

namespace Osm\Admin\Schema\Diff\Property;

use Osm\Core\Attributes\Type;

use Osm\Admin\Schema\Property\String_ as StringPropertyObject;

/**
 * @property \stdClass|StringPropertyObject|null $old
 * @property StringPropertyObject $new
 */
#[Type('string')]
class String_ extends Scalar {
}