<?php

namespace Osm\Admin\Ui;

use Illuminate\Support\Str;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Property $property
 * @property string $title
 * @property ?string $header_template
 * @property ?string $cell_template
 * @property ?string $cell_formula
 */
class Control extends Object_
{
    use RequiredSubTypes;

    protected function get_property(): Property {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        return Str::title($this->property->name);
    }
}