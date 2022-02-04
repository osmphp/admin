<?php

namespace Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property bool $unsigned #[Serialized]
 * @property string $size #[Serialized]
 */
#[Type('int')]
class Int_ extends Scalar
{
    protected function get_unsigned(): bool {
        return false;
    }

    protected function get_size(): string {
        return static::MEDIUM;
    }
}