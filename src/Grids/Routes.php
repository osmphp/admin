<?php

namespace Osm\Admin\Grids;

use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use function Osm\merge;

/**
 * @property Schema $schema
 */
class Routes extends Object_
{
    public function all(): array {
        $routes = [];

        foreach ($this->schema->classes as $class) {
            foreach ($class->grids as $grid) {
                $routes = merge($routes, $grid->routes);
            }
        }

        return $routes;
    }

    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema;
    }
}