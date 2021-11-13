<?php

namespace Osm\Admin\Grids;

use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Class_ $class
 * @property string $area_class_name #[Serialized]
 * @property ?string $name #[Serialized]
 * @property string $url #[Serialized]
 * @property Column[] $columns
 * @property string[] $select #[Serialized]
 * @property array $routes #[Serialized]
 * @property bool $multiselect #[Serialized]
 * @property bool $editable #[Serialized]
 * @property bool $can_create #[Serialized]
 */
class Grid extends Object_
{
    use SubTypes;

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_area_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_url(): string {
        throw new Required(__METHOD__);
    }

    protected function get_columns(): array {
        $columns = [];

        foreach ($this->select as $name) {
            $property = $this->class->properties[$name];

            if ($property->grid_column) {
                $columns[$property->name] = $property->grid_column;
            }
        }

        return $columns;
    }

    protected function get_select(): array {
        throw new Required(__METHOD__);
    }

    protected function get_routes(): array {
        return [
            $this->area_class_name => [
                "GET {$this->url}" => [ Routes\Admin\RenderGridPage::class => [
                    'data_class_name' => $this->class->name,
                    'grid_name' => $this->name,
                ]],
            ],
        ];
    }

    protected function get_name(): string {
        return "{$this->area_class_name}:{$this->url}";
    }
}