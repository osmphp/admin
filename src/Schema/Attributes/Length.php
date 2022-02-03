<?php

namespace Osm\Admin\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Length
{
    public function __construct(
        public ?int $max = null,
        public ?int $min = null,
    )
    {
    }
}