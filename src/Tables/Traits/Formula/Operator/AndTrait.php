<?php

namespace Osm\Admin\Tables\Traits\Formula\Operator;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Formulas\Formula;
use Osm\Admin\Tables\TableQuery;
use Osm\Admin\Tables\Traits\FormulaTrait;
use Osm\Core\Attributes\UseIn;

#[UseIn(Formula\Operator\And_::class)]
trait AndTrait
{
    use FormulaTrait;

    public function tables_filter(TableQuery $query, QueryBuilder $clause): void
    {
        $clause->where(function(QueryBuilder $clause) use ($query) {
            /* @var Formula\Operator\And_|static $this */

            foreach ($this->operands as $operand) {
                $operand->tables_filter($query, $clause);
            }
        });
    }
}