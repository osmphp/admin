<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Length extends Attribute
{
    public function __construct(
        public ?int $max = null,
        public ?int $min = null,
    )
    {
    }

    public function parse(\stdClass|Struct|Property $data): void {
        $data->max_length = $this->max;
    }
}