<?php

namespace Osm\Admin\Tables\Traits\Formula;

use Osm\Admin\Formulas\Formula;
use Osm\Admin\Queries\Query;
use Osm\Admin\Tables\Table;
use Osm\Admin\Tables\TableQuery;
use Osm\Admin\Tables\Traits\FormulaTrait;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property string $tables_alias
 */
#[UseIn(Formula\Identifier::class)]
trait IdentifierTrait
{
    use FormulaTrait;

    public function tables_sql(TableQuery $query,
        string $joinMethod = 'leftJoin'): string
    {
        /* @var Formula\Identifier|static $this */
        $alias = 'this';

        if (!empty($this->accessors)) {
            foreach ($this->accessors as $accessor) {
                $from = $alias;
                $alias = $alias == 'this'
                    ? $accessor->name
                    : "{$alias}__{$accessor->name}";

                if (!isset($query->joins[$alias])) {
                    $query->joins[$alias] = true;
                    $accessor->class->instance->{"join_{$accessor->name}"}(
                        $query, $joinMethod, $from, $alias);
                }
            }
        }

        /* @var Table $table */
        $table = $this->property->class->storage;

        return isset($table->columns[$this->property->name])
            ? "{$alias}.{$this->property->name}"
            : "{$alias}.data->{$this->property->name}";
    }

    public function tables_select(TableQuery $query): void {
        $query->db_query->addSelect(
            "{$this->tables_sql($query)} AS {$this->tables_alias}");
    }

    protected function get_tables_alias(): string {
        /* @var Formula\Identifier|static $this */
        $alias = '';
        foreach ($this->accessors as $accessor) {
            $alias .= "{$accessor->name}__";
        }

        return "$alias{$this->property->name}";
    }
}