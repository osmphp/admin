<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Parser;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $operand #[Serialized]
 *
 * @uses Serialized
 */
class Unary extends Formula
{
    protected function get_operand(): Formula {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->operand->parent = $this;
    }

    public function resolve(Table $table): void
    {
        $this->operand->resolve($table);
        $this->data_type = $this->operand->data_type;
        $this->array = $this->operand->array;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        $operator = match($this->type) {
            Formula::NEGATIVE => '-',
            default => throw new NotImplemented($this),
        };

        return "{$operator}{$this->operand->toSql($bindings, $from, $join)}";
    }
}