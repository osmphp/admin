<?php

namespace Osm\Admin\Ui\Control;

use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\Type;

#[Type('input')]
class Input extends Control
{
    public string $header_template = 'ui::grid.header.input';
    public string $cell_template = 'ui::grid.cell.input';
}