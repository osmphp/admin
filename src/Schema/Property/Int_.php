<?php

namespace Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property bool $unsigned #[Serialized]
 * @property string $size #[Serialized]
 * @property bool $auto_increment #[Serialized]
 *
 * @uses Serialized
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

    protected function get_auto_increment(): bool {
        return false;
    }
}