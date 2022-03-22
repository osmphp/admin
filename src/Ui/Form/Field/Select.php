<?php

namespace Osm\Admin\Ui\Form\Field;

use Osm\Admin\Ui\Form\Field;
use Osm\Core\Attributes\Type;

#[Type('select')]
class Select extends Field
{
    public string $template = 'ui::form.field.select';

    protected function get_data(): array {
        return array_merge(parent::get_data(), [
            'value' => $this->form->item->{$this->name},
            'options' => $this->property->options,
            'js' => [

            ],
        ]);
    }
}