<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property string $value #[Serialized]
 * @property int $token #[Serialized]
 *
 * @uses Serialized
 */
class Literal extends Formula
{
    public $type = self::LITERAL;

    protected function get_value(): string {
        throw new Required(__METHOD__);
    }

    protected function get_token(): int {
        throw new Required(__METHOD__);
    }
}