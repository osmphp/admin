<?php

namespace Osm\Admin\Ui;

use Illuminate\Support\Str;
use Osm\Admin\Schema\DataType;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;

/**
 * @property DataType $data_type
 * @property ?Property $property
 * @property string[] $supported_facets #[Serialized]
 * @property ?Facet $default_facet #[Serialized]
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

    protected function get_data_type(): DataType {
        throw new Required(__METHOD__);
    }

    protected function get_supported_facets(): array {
        return ['checkboxes'];
    }

    protected function get_default_facet(): ?Facet {
        return Facet\Checkboxes::new();
    }

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

    public function display(\stdClass $item): ?string {
        return $item->{$this->name} ?? null;
    }
}