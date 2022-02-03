<?php

namespace Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property ?int $max_length #[Serialized]
 */
class String_ extends Scalar
{
    protected function get_max_length(): ?int {
        throw new NotImplemented($this);
    }

}