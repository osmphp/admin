<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Of
{
    public function __construct(public string $class_name)
    {
    }
}