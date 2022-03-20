<?php

namespace Osm\Admin\Ui\Form;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Blade\Attributes\RenderTime;

/**
 * @property Fieldset $fieldset
 * @property Form $form
 * @property string $name #[Serialized]
 * @property string $formula #[Serialized]
 * @property Formula\SelectExpr $parsed_formula #[RenderTime]
 *
 * @uses Serialized, RenderTime
 *
 */
class Field extends View
{
    protected function get_fieldset(): Fieldset {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_formula(): string {
        return $this->name;
    }

    protected function get_form(): Form {
        return $this->fieldset->form;
    }

    protected function get_parsed_formula(): Formula\SelectExpr {
        return $this->form->query->selects[$this->name];
    }

    protected function get_template(): string {
        throw new Required(__METHOD__);
    }

    public function prepare(Query $query): void
    {
        $query->select($this->formula);
    }
}