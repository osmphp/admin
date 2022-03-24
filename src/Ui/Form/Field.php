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
use function Osm\__;
use function Osm\merge;

/**
 * @property Control $control
 * @property ?Fieldset $fieldset
 * @property ?Form $form
 * @property ?string $name #[Serialized]
 * @property ?string $formula #[Serialized]
 * @property Formula\SelectExpr $select #[RenderTime]
 * @property bool $multiple #[RenderTime]
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
        $data = [
            'name' => $this->name,
            'title' => $this->property->title,
            'multiple' => $this->multiple,
            'js' => [
                'edit' => $this->form->edit,
                'multiple' => $this->multiple,
            ],
        ];

        if ($this->multiple) {
            $data = merge($data, [
                'js' => [
                    's_empty' => __("<empty>"),
                    's_multiple_values' => __("<multiple values>"),
                ],
            ]);
        }

        return $data;
    }

    protected function get_multiple(): bool {
        return in_array($this->name, $this->form->item->_multiple ?? []);
    }

    protected function get_property(): Property {
        return $this->form->struct->properties[$this->name];
    }

    public function merge(\stdClass $merged): void
    {
        if (!$this->form->merge) {
            $merged->_multiple[] = $this->name;
            return;
        }

        $value = null;
        foreach ($this->form->result->items as $index => $item) {
            if ($index == 0) {
                $value = $item->{$this->name} ?? null;
                continue;
            }

            if ($value !== ($item->{$this->name} ?? null)) {
                $merged->_multiple[] = $this->name;
                return;
            }
        }

        if ($value !== null) {
            $merged->{$this->name} = $value;
        }
    }
}