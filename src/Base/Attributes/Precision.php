<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Precision
{
    public function __construct(public int $precision)
    {
    }
}