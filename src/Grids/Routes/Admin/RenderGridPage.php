<?php

namespace Osm\Admin\Grids\Routes\Admin;

use Osm\Admin\Grids\Grid;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

/**
 * @property string $data_class_name
 * @property ?string $grid_name
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

    protected function get_grid(): Grid {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema
            ->classes[$this->data_class_name]
            ->grids[$this->grid_name];
    }
}