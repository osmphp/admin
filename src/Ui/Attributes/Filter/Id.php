<?php

namespace Osm\Admin\Ui\Attributes\Filter;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Ui\Filter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Id extends Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void
    {
        $data->filter = Filter\Id::new();
    }
}