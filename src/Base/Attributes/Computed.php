<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Computed
{
    public function __construct(public string $formula)
    {
    }
}