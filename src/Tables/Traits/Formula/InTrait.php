<?php

namespace Osm\Admin\Tables\Traits\Formula;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Formulas\Formula;
use Osm\Admin\Tables\TableQuery;
use Osm\Admin\Tables\Traits\Formula\Operator\EqualsTrait;
use Osm\Admin\Tables\Traits\FormulaTrait;
use Osm\Core\Attributes\UseIn;

#[UseIn(Formula\In_::class)]
trait InTrait
{
    use FormulaTrait;

    public function tables_filter(TableQuery $query, QueryBuilder $clause): void
    {
        /* @var Formula\In_|static $this */
        $clause->whereIn(
            $this->value->tables_sql($query, joinMethod: 'join'),
            array_map(fn(Formula\Literal $item) => $item->tables_value(),
                $this->items)
        );
    }
}