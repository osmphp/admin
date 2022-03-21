<?php

namespace Osm\Admin\Ui\Grid\Column;

use Osm\Admin\Ui\Grid\Column;
use Osm\Core\Attributes\Type;
use function Osm\__;

#[Type('select')]
class Select extends Column
{
    public string $template = 'ui::grid.header.select';
    public string $cell_template = 'ui::grid.cell.select';

    public function display(\stdClass $item): ?string
    {
        if (($value = parent::display($item)) === null) {
            return null;
        }

        return __($this->control->property->options[$value]->title);
    }
}