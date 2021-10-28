<?php

namespace Osm\Data\Tables\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Data\Schema\Property;
use Osm\Data\Tables\Attributes\Column;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Column $column #[Serialized]
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_column(): ?Column {
        /* @var Property|static $this */
        foreach ($this->reflection->attributes as $attribute) {
            if ($attribute instanceof Column) {
                return $attribute;
            }
        }

        return null;
    }
}