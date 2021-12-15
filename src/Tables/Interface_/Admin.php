<?php

namespace Osm\Admin\Tables\Interface_;

use Osm\Admin\Interfaces\Interface_\Admin as BaseAdmin;
use Osm\Core\Attributes\Type;
use Osm\Framework\Areas\Attributes\Area;

#[Type('table_admin'), Area(\Osm\Framework\Areas\Admin::class)]
class Admin extends BaseAdmin
{
}