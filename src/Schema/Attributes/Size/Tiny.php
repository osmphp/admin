<?php

namespace Osm\Admin\Schema\Attributes\Size;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Tiny extends Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void {
        $data->size = Property::TINY;
    }
}