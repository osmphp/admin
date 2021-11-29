<?php

namespace Osm\Admin\Tables\Traits;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Formulas\Formula;
use Osm\Admin\Queries\Query;
use Osm\Admin\Tables\Table;
use Osm\Admin\Tables\TableQuery;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Formula::class)]
trait FormulaTrait
{
    public function tables_sql(TableQuery $query,
        string $joinMethod = 'leftJoin'): string
    {
        throw new NotImplemented($this);
    }

    public function tables_value(): mixed {
        throw new NotImplemented($this);
    }

    public function tables_filter(TableQuery $query, QueryBuilder $clause): void {
        throw new NotImplemented($this);
    }

    public function tables_select(TableQuery $query): void {
        throw new NotImplemented($this);
    }

    public function tables_order(TableQuery $query): void {
        throw new NotImplemented($this);
    }
}