<?php

namespace Osm\Admin\Ui\View;

use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Query;
use function Osm\ui_query;

/**
 * @property \Osm\Admin\Ui\List_ $model
 * @property Table $table
 * @property Query $query
 */
class List_ extends View
{
    protected function get_table(): Table {
        return $this->model->table;
    }

    protected function get_query(): Query {
        $query = ui_query($this->model->table->name)
            ->all()
            ->url($this->http_query, 'id', 'select')
            ->count();

        $query->query->select('id');

        return $query;
    }
}