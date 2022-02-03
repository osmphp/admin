<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Table
{
    public function __construct(public string $name)
    {
    }
}