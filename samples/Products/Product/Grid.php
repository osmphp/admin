<?php

namespace Osm\Admin\Samples\Products\Product;

use Osm\Admin\Samples\Products\Product;
use Osm\Admin\Schema\Attributes\Class_;
use Osm\Admin\Ui\Attributes\View;
use Osm\Admin\Ui\List_\Grid as BaseGrid;

#[Class_(Product::class), View('list')]
class Grid extends BaseGrid
{
    /**
     * Returns formulas shown as grid columns
     *
     * @return string[]
     */
    protected function get_selects(): array
    {
        return ['title', 'color'];
    }
}