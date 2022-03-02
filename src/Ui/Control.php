<?php

namespace Osm\Admin\Ui;

use Illuminate\Support\Str;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $header_template #[Serialized]
 * @property string $cell_template #[Serialized]
 *
 * Render-time properties:
 *
 * @property View $view
 * @property string $name
 * @property string $title
 *
 * @uses Serialized
 */
class Control extends Object_
{
    use RequiredSubTypes;

    protected function get_header_template(): string {
        throw new Required(__METHOD__);
    }

    protected function get_cell_template(): string {
        throw new Required(__METHOD__);
    }

    protected function get_view(): string {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        return Str::title($this->name);
    }
}