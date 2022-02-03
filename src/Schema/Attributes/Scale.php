<?php

namespace Osm\Admin\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Scale
{
    public function __construct(public int $scale)
    {
    }
}