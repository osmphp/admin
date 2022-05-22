<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $expr #[Serialized]
 *
 * @uses Serialized
 */
class Cast extends Formula
{
    public $type = self::CAST;

    protected function get_expr(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_formula(): string
    {
        return $this->expr->formula;
    }

    protected function get_pos(): int
    {
        return $this->expr->pos;
    }

    protected function get_length(): int
    {
        return $this->expr->length;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        return $this->data_type->castToSql($this->expr, $bindings, $from, $join);
    }
}