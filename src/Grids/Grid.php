<?php

namespace Osm\Admin\Grids;

use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $data_class_name #[Serialized]
 * @property string $area_class_name #[Serialized]
 * @property ?string $name #[Serialized]
 * @property string $url #[Serialized]
 * @property Column[] $columns #[Serialized]
 * @property string[] $select #[Serialized]
 * @property Route[] $routes #[Serialized]
 * @property Class_ $data_class
 * @property Column[] $selected_columns
 */
class Grid extends Object_
{
    protected function get_data_class_name(): string {
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

        foreach ($this->data_class->properties as $property) {
            if ($property->grid_column) {
                $columns[$property->name] = $property->grid_column;
            }
        }

        return $columns;
    }

    protected function get_select(): array {
        throw new Required(__METHOD__);
    }

    protected function get_data_class(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$this->data_class_name];
    }

    protected function get_routes(): array {
        return [
            $this->area_class_name => [
                "GET {$this->url}/" => [ Routes\Admin\RenderGridPage::class => [
                    'data_class_name' => $this->data_class_name,
                    'grid_name' => $this->name,
                ]],
            ],
        ];
    }

    protected function get_name(): string {
        return "{$this->area_class_name}:{$this->url}";
    }

    protected function get_selected_columns(): array {
        $columns = [];

        foreach ($this->select as $name) {
            $columns[$name] = $this->columns[$name];
        }

        return $columns;
    }
}