<?php

namespace Osm\Admin\Tables\Traits\Formula\Operator;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Formulas\Formula;
use Osm\Admin\Tables\TableQuery;
use Osm\Admin\Tables\Traits\FormulaTrait;
use Osm\Core\Attributes\UseIn;

#[UseIn(Formula\Operator\NotEquals::class)]
trait NotEqualsTrait
{
    use FormulaTrait;

    public function tables_filter(TableQuery $query, QueryBuilder $clause): void
    {
        /* @var Formula\Operator\NotEquals|static $this */
        $clause->where(
            $this->operands[0]->tables_sql($query, joinMethod: 'join'),
            '<>',
            $this->operands[1]->tables_value()
        );
    }
}