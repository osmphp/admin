<?php

namespace Osm\Admin\Ui\Form;

use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Blade\Attributes\RenderTime;
use function Osm\view;

/**
 * @property Section $section
 * @property Form $form
 * @property string $name #[Serialized]
 * @property array $layout #[Serialized]
 * @property Field[] $fields #[Serialized]
 *
 * @uses Serialized, RenderTime
 */
class Fieldset extends View
{
    public string $template = 'ui::form.fieldset';

    protected function get_section(): Section {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_layout(): array {
        return [];
    }

    protected function get_data(): array {
        return [
            'fieldset' => $this,
        ];
    }

    protected function get_fields(): array {
        $fields = [];

        foreach ($this->form->struct->properties as $property) {
            if (!$property->control) {
                continue;
            }

            $fields[$property->name] = $field =
                clone $property->control->form_field;

            $field->fieldset = $this;
            $field->name = $property->name;
            $field->formula = $property->name;
        }

        return $fields;
    }


    public function __wakeup(): void
    {
        foreach ($this->fields as $field) {
            $field->fieldset = $this;
        }
    }

    protected function get_form(): Form {
        return $this->section->form;
    }

    public function prepare(Query $query): void
    {
        foreach ($this->fields as $field) {
            $field->prepare($query);
        }
    }
}