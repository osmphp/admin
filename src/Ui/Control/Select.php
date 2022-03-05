<?php

namespace Osm\Admin\Ui\Control;

use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\Type;
use function Osm\__;

#[Type('select')]
class Select extends Control
{
    public string $header_template = 'ui::grid.header.select';
    public string $cell_template = 'ui::grid.cell.select';

    public function display(\stdClass $item): ?string
    {
        if (($value = parent::display($item)) === null) {
            return null;
        }

        return __($this->property->options[$value]->title);
    }
}