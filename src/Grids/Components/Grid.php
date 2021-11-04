<?php

namespace Osm\Admin\Grids\Components;

use Osm\Admin\Grids\Grid as GridData;
use Osm\Framework\Blade\Component;

class Grid extends Component
{
    public string $__template = 'grids::components.grid';

    public function __construct(public GridData $grid)
    {
    }
}