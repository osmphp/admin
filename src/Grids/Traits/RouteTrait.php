<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Forms\Form;
use Osm\Admin\Grids\Grid;
use Osm\Admin\Interfaces\Route;
use Osm\Core\Attributes\UseIn;

/**
 * @property Grid $grid
 */
#[UseIn(Route::class)]
trait RouteTrait
{
    protected function get_grid(): Grid {
        /* @var Route|static $this */
        return $this->interface->grid;
    }
}