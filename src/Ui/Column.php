<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Property;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
/**
 * @property Grid $grid
 * @property string $property_name #[Serialized]
 * @property Property $property
 * @property ?string $header_template #[Serialized]
 * @property ?string $cell_template #[Serialized]
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property ?string $formula #[Serialized]
 *
 * @uses Serialized
 */
class Column extends Object_
{
    protected function get_grid(): Grid {
        throw new Required(__METHOD__);
    }

    protected function get_property_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_property(): Property {
        return $this->grid->table->properties[$this->property_name];
    }

    protected function get_header_template(): ?string {
        return $this->property->control->header_template;
    }

    protected function get_cell_template(): ?string {
        return $this->property->control->cell_template;
    }

    protected function get_title(): string {
        return $this->property->control->title;
    }

    protected function get_name(): string {
        return $this->property->name;
    }

    protected function get_formula(): ?string {
        return $this->property->control->cell_formula;
    }
}