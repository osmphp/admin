<?php

namespace Osm\Admin\Ui\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Filterable extends Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void
    {
        $data->filterable = true;
    }
}