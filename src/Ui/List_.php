<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\Required;
use function Osm\ui_query;

/**
 * @property Table $table
 * @property Query $query
 */
class List_ extends View
{
    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_query(): Query {
        $query = ui_query($this->table->name)
            ->all()
            ->url($this->http_query, 'id', 'select')
            ->count();

        $query->query->select('id');

        return $query;
    }
}