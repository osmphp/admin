<?php

namespace Osm\Admin\Ui\Form\Field;

use Osm\Admin\Ui\Form\Field;
use Osm\Core\Attributes\Type;

#[Type('select')]
class Select extends Field
{
    public string $template = 'ui::form.field.input';
}