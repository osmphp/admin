<?php

namespace Osm\Admin\Ui\Form;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Blade\Attributes\RenderTime;

/**
 * @property Control $control
 * @property ?Fieldset $fieldset
 * @property ?Form $form
 * @property ?string $name #[Serialized]
 * @property ?string $formula #[Serialized]
 * @property Formula\SelectExpr $select #[RenderTime]
 * @property Property $property
 *
 * @uses Serialized, RenderTime
 *
 */
class Field extends View
{
    use RequiredSubTypes;

    protected function get_control(): Control {
        throw new Required(__METHOD__);
    }

    protected function get_form(): ?Form {
        return $this->fieldset?->form;
    }

    protected function get_select(): Formula\SelectExpr {
        return $this->form->query->selects[$this->name];
    }

    public function prepare(Query $query): void
    {
        $query->select($this->formula);
    }

    protected function get_data(): array {
        return [
            'name' => $this->name,
            'title' => $this->property->title,
            'value' => $this->form->item->{$this->name},
            'multiple' => false,
            'js' => [

            ],
        ];
    }

    protected function get_property(): Property {
        return $this->form->struct->properties[$this->name];
    }
}