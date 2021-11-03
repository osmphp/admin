<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS), Object_]
final class Table
{
    public function __construct(public string $name)
    {
    }
}