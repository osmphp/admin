<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $expr #[Serialized]
 * @property string $alias #[Serialized]
 */
class SelectExpr extends Formula
{
    public $type = self::SELECT_EXPR;

    protected function get_expr(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_alias(): string {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->expr->parent = $this;
    }
}