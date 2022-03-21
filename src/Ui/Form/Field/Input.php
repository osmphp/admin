<?php

namespace Osm\Admin\Ui\Form\Field;

use Osm\Admin\Ui\Form\Field;
use Osm\Core\Attributes\Type;

#[Type('input')]
class Input extends Field
{
    public string $template = 'ui::form.field.input';
}