<?php

namespace Osm\Admin\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Precision
{
    public function __construct(public int $precision)
    {
    }
}