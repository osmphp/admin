<?php

namespace Osm\Admin\Ui\Grid\Column;

use Osm\Admin\Ui\Grid\Column;
use Osm\Core\Attributes\Type;

#[Type('input')]
class Input extends Column
{
    public string $template = 'ui::grid.header.input';
    public string $cell_template = 'ui::grid.cell.input';
}