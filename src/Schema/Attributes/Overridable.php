<?php

namespace Osm\Admin\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Overridable
{
    public function __construct(public string $formula)
    {
    }
}