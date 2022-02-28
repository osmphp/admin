<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\Required;
use function Osm\ui_query;

/**
 * @property Table $table
 */
class List_ extends View
{
    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }
}