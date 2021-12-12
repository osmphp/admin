<?php

namespace Osm\Admin\Interfaces\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Interfaces\Interface_\Admin;
use Osm\Admin\Interfaces\Route;
use Osm\Core\Attributes\Name;

#[Interface_(Admin::class), Name('GET /')]
class GridPage extends Route
{

}