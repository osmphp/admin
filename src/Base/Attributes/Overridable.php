<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Overridable
{
    public function __construct(public string $formula)
    {
    }
}