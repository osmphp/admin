<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Default_
{
    public function __construct(public mixed $value)
    {
    }
}