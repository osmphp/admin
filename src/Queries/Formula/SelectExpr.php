<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $expr #[Serialized]
 * @property string $alias #[Serialized]
 *
 * @uses Serialized
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

    public function resolve(Table $table): void
    {
        $this->expr->resolve($table);

        $this->data_type = $this->expr->data_type;
        $this->array = $this->expr->array;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        return $this->expr->toSql($bindings, $from, $join) .
            " AS `{$this->alias}`";
    }
}