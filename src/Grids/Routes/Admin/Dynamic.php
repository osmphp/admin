<?php

namespace Osm\Admin\Grids\Routes\Admin;

use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Areas\Attributes\Area;
use Osm\Framework\Http\Route;

/**
 * @property Schema $schema
 */
#[Area(Admin::class)]
class Dynamic extends Route
{
    public function match(): ?Route
    {
        $routeName = "{$this->http->request->getMethod()} {$this->http->path}";

        if (!($route = $this->schema->grid_routes
                ["{$this->http->area->name}:{$routeName}"] ?? null))
        {
            return null;
        }

        $new = "{$route->class_name}::new";
        return $new([
            'data_class_name' => $route->data_class_name,
            'grid_name' => $route->grid_name,
        ]);
    }

    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema;
    }
}