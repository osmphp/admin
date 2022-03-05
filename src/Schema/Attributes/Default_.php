<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Default_ extends Attribute
{
    public function __construct(public mixed $value)
    {
    }

    public function parse(\stdClass|Struct|Property|Option $data): void {
        $data->default = $this->value;
    }
}