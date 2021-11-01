<?php

namespace Osm\Data\Samples\Orders\Routes\Admin;

use Osm\Core\Attributes\Name;
use Osm\Data\Grids\Routes\GridPage;
use Osm\Data\Queries\Attributes\Of;
use Osm\Data\Samples\Orders\Orders as OrderTable;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Areas\Attributes\Area;

#[Area(Admin::class), Name('GET /orders/'), Of(OrderTable::class)]
class Orders extends GridPage
{

}