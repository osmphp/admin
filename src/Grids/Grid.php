<?php

namespace Osm\Admin\Grids;

use Osm\Admin\Base\Attributes\Markers\Grid\Column as ColumnMarker;
use Osm\Admin\Interfaces\Interface_;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Interface_ $interface
 * @property Class_ $class
 * @property Column[] $columns #[Serialized]
 * @property string[] $select #[Serialized]
 */
class Grid extends Object_
{
    public string $template = 'grids::grid';

    protected function get_interface(): Interface_ {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        return $this->interface->class;
    }

    protected function get_columns(): array {
        global $osm_app; /* @var App $osm_app */

        $fields = [];

        foreach ($this->select as $identifier) {
            $property = $this->interface->class->properties[$identifier];

            foreach ($property->reflection->attributes as
                     $attributeClassName => $attribute)
            {
                if (!($attributeClass = $osm_app->classes[$attributeClassName]
                    ?? null))
                {
                    continue;
                }

                /* @var ColumnMarker $marker */
                if (!($marker = $attributeClass->attributes[ColumnMarker::class]
                    ?? null))
                {
                    continue;
                }

                $data = (array)$attribute;

                $new = $osm_app->classes[Column::class]
                        ->getTypeClassName($marker->type) . "::new";

                $fields[$property->name] = $new(array_merge([
                    'grid' => $this,
                    'name' => $property->name,
                ], $data));

                break;
            }
        }

        return $fields;
    }

    protected function get_select(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->columns as $column) {
            $column->grid = $this;
        }
    }
}