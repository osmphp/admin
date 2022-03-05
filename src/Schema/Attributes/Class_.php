<?php

namespace Osm\Admin\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Class_
{
    public function __construct(public string $class_name)
    {
    }
}