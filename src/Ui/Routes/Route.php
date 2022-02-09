<?php

namespace Osm\Admin\Ui\Routes;

use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Query;
use Osm\Core\App;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Http\Route as BaseRoute;
use function Osm\ui_query;

/**
 * @property string $route_name
 * @property string $class_name
 * @property Table $table
 * @property Query $query
 */
class Route extends BaseRoute
{
    protected function get_route_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_table(): Table {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->tables[$this->class_name];
    }

    protected function get_query(): Query {
        return ui_query($this->class_name);
    }
}