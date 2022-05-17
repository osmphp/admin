<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $condition #[Serialized]
 * @property Formula $then #[Serialized]
 * @property Formula $else_ #[Serialized]
 *
 * @uses Serialized
 */
class Ternary extends Formula
{
    public $type = self::TERNARY;

    protected function get_condition(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_then(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_else_(): Formula {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->condition->parent = $this;
        $this->then->parent = $this;
        $this->else_->parent = $this;
    }

    public function resolve(Table $table): void
    {
        $this->condition->resolve($table);
        $this->then->resolve($table);
        $this->else_->resolve($table);

        $this->condition = $this->condition->castTo('bool');
        $this->castAllToFirstNonNull([&$this->then, &$this->else_]);

        $this->data_type = $this->then->data_type;
        $this->array = false;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        return "IF({$this->condition->toSql($bindings, $from, $join)}, ".
            "{$this->then->args[1]->toSql($bindings, $from, $join)}, " .
            "{$this->else_->toSql($bindings, $from, $join)})";
    }
}