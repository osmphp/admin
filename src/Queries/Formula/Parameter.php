<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property mixed $parameter
 * @property int $index #[Serialized]
 *
 * @uses Serialized
 */
class Parameter extends Formula
{
    public $type = self::PARAMETER;

    protected function get_parameter(): mixed {
        throw new Required(__METHOD__);
    }

    protected function get_index(): int {
        throw new Required(__METHOD__);
    }}