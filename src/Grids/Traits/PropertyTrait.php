<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Base\Attributes\Grid\Column as ColumnAttribute;
use Osm\Admin\Grids\Column;
use Osm\Admin\Schema\Property;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Column $grid_column #[Serialized]
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_grid_column(): ?Column {
        global $osm_app; /* @var App $osm_app */
        /* @var Property|static $this */

        $columnClassNames = $osm_app->descendants->byName(Column::class);

        foreach ($this->reflection->attributes as $className => $attribute) {
            if (!($class = $osm_app->classes[$className] ?? null)) {
                continue;
            }

            if (!($marker = $class->attributes[ColumnAttribute::class] ?? null)) {
                continue;
            }

            $new = "{$columnClassNames[$marker->type]}::new";

            return $new(array_merge(['name' => $this->name], (array)$attribute));
        }

        return null;
    }
}