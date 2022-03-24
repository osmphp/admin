<?php

namespace Osm\Admin\Ui\Form\Field;

use Osm\Admin\Ui\Form\Field;
use Osm\Core\Attributes\Type;
use function Osm\merge;

#[Type('select')]
class Select extends Field
{
    public string $template = 'ui::form.field.select';

    protected function get_data(): array {
        return merge(parent::get_data(), [
            'value' => $this->form->item->{$this->name} ?? null,
            'options' => $this->property->options,
        ]);
    }
}