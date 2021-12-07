<?php

namespace Osm\Admin\Grids\Routes\Admin;

use Osm\Admin\Grids\Grid;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

/**
 * @property string $class_name
 * @property string $grid_name
 * @property Class_ $class
 * @property Grid $grid
 */
class RenderGridPage extends Route
{
    public function run(): Response
    {
        return view_response('grids::pages.grid', [
            'grid' => $this->grid,
        ]);
    }

    protected function get_class(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$this->class_name];
    }

    protected function get_grid(): Grid {
        return $this->class->grids[$this->grid_name];
    }
}