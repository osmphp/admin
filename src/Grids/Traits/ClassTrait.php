<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Base\Attributes\Markers\Grid as GridMarker;
use Osm\Admin\Grids\Grid;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Areas\Admin;

/**
 * @property Grid[] $grids #[Serialized]
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_grids(): array {
        /* @var Class_|static $this */
        $grids = [];

        foreach ($this->reflection->attributes as $className => $attributes) {
            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }
            $grids = array_merge($grids,
                $this->createGrids($className, $attributes));
        }

        return $grids;
    }

    protected function createGrids(string $attributeClassName,
        array $attributes): array
    {
        global $osm_app; /* @var App $osm_app */
        /* @var Class_|static $this */

        if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
            return [];
        }

        /* @var GridMarker $marker */
        if (!($marker = $class->attributes[GridMarker::class] ?? null)) {
            return [];
        }

        $gridClassNames = $osm_app->descendants->byName(Grid::class);
        $new = "{$gridClassNames[$marker->type]}::new";

        $grids = [];

        foreach ($attributes as $attribute) {
            $grid = $new(array_merge(['data_class_name' => $this->name],
                (array)$attribute));
            $grids[$grid->name] = $grid;
        }

        return $grids;
    }
}