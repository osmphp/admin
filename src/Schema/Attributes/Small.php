<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Small extends Attribute
{
    public function parse(\stdClass|Struct|Property $data): void {
        $data->size = Property::SMALL;
    }
}