<?php

namespace Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property bool $unsigned #[Serialized]
 * @property string $size #[Serialized]
 */
class Int_ extends Scalar
{
    protected function get_unsigned(): bool {
        throw new NotImplemented($this);
    }

    protected function get_size(): string {
        throw new NotImplemented($this);
    }
}