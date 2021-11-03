<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Grids\Column;
use Osm\Admin\Schema\Property;
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
        throw new NotImplemented($this);
    }
}