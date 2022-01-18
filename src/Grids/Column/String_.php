<?php

namespace Osm\Admin\Grids\Column;

use Osm\Admin\Grids\Column;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\Type;

#[Type('string')]
class String_ extends Column
{
    public string $header_template = 'grids::header.string';
    public string $template = 'grids::cell.string';

}