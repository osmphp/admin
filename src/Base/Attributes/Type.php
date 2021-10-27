<?php

namespace Osm\Data\Base\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Type
{
    public function __construct(public string $name)
    {
    }
}