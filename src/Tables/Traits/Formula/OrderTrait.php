<?php

namespace Osm\Admin\Tables\Traits\Formula;

use Osm\Admin\Formulas\Formula;
use Osm\Admin\Tables\TableQuery;
use Osm\Admin\Tables\Traits\FormulaTrait;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Formula\Order::class)]
trait OrderTrait
{
    use FormulaTrait;

    public function tables_order(TableQuery $query): void {
        /* @var Formula\Order|static $this */
        $query->db_query->orderBy($this->expr->tables_sql($query),
            $this->desc ? 'desc' : 'asc');
    }
}