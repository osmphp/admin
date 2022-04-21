<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
final class Rename extends Attribute
{
    public function __construct(public string $name)
    {
    }

    public function parse(\stdClass|Struct|Property|Option $data): void {
        $data->rename = $this->name;
    }
}