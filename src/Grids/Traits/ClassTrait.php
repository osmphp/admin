<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Base\Attributes\Grid as GridAttribute;
use Osm\Admin\Grids\Grid;
use Osm\Admin\Schema\Class_;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $default_grid_url #[Serialized]
 * @property Grid[] $grids #[Serialized]
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_grids(): array {
        /* @var Class_|static $this */
        $grids = [];

        if ($this->default_grid_url) {
            $grids['admin:'] = Grid::new([
                'data_class_name' => $this->name,
                'area_name' => 'admin',
                'url' => $this->default_grid_url,
            ]);
        }

        return $grids;
    }

    protected function get_default_grid_url(): ?string {
        /* @var Class_|static $this */

        /* @var GridAttribute $grid */
        return ($grid = $this->reflection->attributes[GridAttribute::class] ?? null)
            ? $grid->url
            : null;
    }
}