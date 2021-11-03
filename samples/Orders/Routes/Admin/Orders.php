<?php

namespace Osm\Admin\Samples\Orders\Routes\Admin;

use Osm\Core\Attributes\Name;
use Osm\Admin\Grids\Routes\GridPage;
use Osm\Admin\Queries\Attributes\Of;
use Osm\Admin\Samples\Orders\Orders as OrderTable;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Areas\Attributes\Area;

#[Area(Admin::class), Name('GET /orders/'), Of(OrderTable::class)]
class Orders extends GridPage
{

}