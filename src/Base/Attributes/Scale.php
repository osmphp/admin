<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Scale
{
    public function __construct(public int $scale)
    {
    }
}