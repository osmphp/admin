<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Grids\Route;
use Osm\Admin\Schema\Schema;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;

/**
 * @property Route[] $grid_routes #[Serialized]
 */
#[UseIn(Schema::class)]
trait SchemaTrait
{
    protected function get_grid_routes(): array {
        /* @var Schema|static $this */

        $routes = [];

        foreach ($this->classes as $class) {
            foreach ($class->grids as $grid) {
                $routes = array_merge($routes, $grid->routes);
            }
        }

        return $routes;
    }
}