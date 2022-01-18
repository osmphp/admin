<?php

namespace Osm\Admin\Grids\Column;

use Osm\Admin\Grids\Column;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\Type;

#[Type('int')]
class Int_ extends Column
{
    public string $header_template = 'grids::header.int';
    public string $template = 'grids::cell.int';

}