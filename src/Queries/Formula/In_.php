<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Data\Formulas\Types;

/**
 * @property Formula $value #[Serialized]
 * @property Formula[] $items #[Serialized]
 *
 * @uses Serialized
 */
class In_ extends Formula
{
    protected function get_value(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_items(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->value->parent = $this;

        foreach ($this->items as $item) {
            $item->parent = $this;
        }
    }

    public function resolve(Table $table): void
    {
        $this->value->resolve($table);

        foreach ($this->items as $item) {
            // TODO: cast to value data type
            $item->resolve($table);
        }
        $this->data_type = 'bool';
        $this->array = false;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        $valueSql = "{$this->value->toSql($bindings, $from, $join)}";
        $operatorSql = $this->type == Formula::NOT_IN ? "NOT IN": "IN";
        $itemSql = '';
        foreach ($this->items as $item) {
            if ($itemSql) {
                $itemSql .= ", ";
            }
            $itemSql .= $item->toSql($bindings, $from, $join);
        }

        return "{$valueSql} {$operatorSql} ($itemSql)";
    }
}