<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property string[] $parts #[Serialized]
 *
 * Resolved properties:
 *
 * @property string $column #[Serialized]
 * @property string $table #[Serialized]
 */
class Identifier extends Formula
{
    public $type = self::IDENTIFIER;

    protected function get_parts(): array {
        throw new Required(__METHOD__);
    }

    protected function get_column(): string {
        throw new Required(__METHOD__);
    }

    protected function get_table(): string {
        throw new Required(__METHOD__);
    }
}