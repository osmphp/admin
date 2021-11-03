<?php

namespace Osm\Admin\Schema\Diff;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Admin\Schema\Class_ as SchemaClass;

/**
 * @property SchemaClass $old
 * @property SchemaClass $new
 *
 * @property Property[] $properties
 */
class Class_ extends Object_
{
    protected function get_properties(): array {
        $propertyDiffs = [];

        if ($this->old) {
            // actual comparison will come later
            throw new NotImplemented($this);
        }

        foreach ($this->new->properties as $property) {
            $propertyDiffs[$property->name] = Property::new([
                'new' => $property,
            ]);
        }

        return $propertyDiffs;
    }
}