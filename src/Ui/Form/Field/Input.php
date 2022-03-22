<?php

namespace Osm\Admin\Ui\Form\Field;

use Osm\Admin\Ui\Form\Field;
use Osm\Core\Attributes\Type;

#[Type('input')]
class Input extends Field
{
    public string $template = 'ui::form.field.input';

    protected function get_data(): array {
        return array_merge(parent::get_data(), [
            'value' => $this->form->item->{$this->name},
            'js' => [

            ],
        ]);
    }
}