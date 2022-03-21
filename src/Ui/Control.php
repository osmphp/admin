<?php

namespace Osm\Admin\Ui;

use Illuminate\Support\Str;
use Osm\Admin\Schema\DataType;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Admin\Ui\Grid;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;

/**
 * @property DataType $data_type
 * @property ?Property $property
 * @property string[] $supported_facets #[Serialized]
 * @property ?Facet $default_facet #[Serialized]
 * @property ?Grid\Column $grid_column #[Serialized]
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

    protected function get_grid_column(): Grid\Column {
        global $osm_app; /* @var App $osm_app */

        $className = $osm_app->descendants
            ->byName(Grid\Column::class, Type::class)
            [$this->type];

        $new = "{$className}::new";

        return $new(['control' => $this]);
    }

    public function __wakeup(): void
    {
        $this->grid_column->control = $this;
    }
}