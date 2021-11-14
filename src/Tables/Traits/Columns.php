<?php

namespace Osm\Admin\Tables\Traits;
use Osm\Admin\Base\Attributes\Markers\Table\Column as ColumnMarker;
use Osm\Admin\Storages\Storage;
use Osm\Admin\Tables\Column;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;

/**
 * @property Column[] $columns #[Serialized]
 */
trait Columns
{
    protected function get_columns(): array {
        global $osm_app; /* @var App $osm_app */
        /* @var Storage $this */

        $columns = [];

        foreach ($this->class->properties as $property) {
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

                $new = $osm_app->classes[Column::class]
                        ->getTypeClassName($marker->type) . "::new";

                $columns[$property->name] = $new(array_merge([
                    'table' => $this,
                    'name' => $property->name,
                ], (array)$attribute));

                break;
            }
        }

        return $columns;
    }

    protected function wakeupColumns(): void {
        foreach ($this->columns as $column) {
            $column->table = $this;
        }
    }
}