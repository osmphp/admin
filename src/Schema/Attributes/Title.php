<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY |
    \Attribute::TARGET_CLASS_CONSTANT)]
final class Title extends Attribute
{
    public function __construct(public string $title)
    {
    }

    public function parse(\stdClass|Struct|Property|Option $data): void {
        $data->title = $this->title;
    }
}