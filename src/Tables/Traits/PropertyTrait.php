<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Tables\Column;

/**
 * @property ?Column $column #[Serialized]
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_column(): ?Column {
        global $osm_app; /* @var App $osm_app */

        /* @var Property|static $this */
        $columnTypesNames = $this->class->schema->table_column_type_names;
        $columnClassNames = $osm_app->descendants->byName(Column::class);

        foreach ($this->reflection->attributes as $className => $attribute) {
            if (!($typeName = $columnTypesNames[$className] ?? null)) {
                continue;
            }

            $new = "{$columnClassNames[$typeName]}::new";
            return $new(array_merge(['property' => $this], (array)$attribute));
        }

        return null;
    }
}