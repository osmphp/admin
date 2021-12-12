<?php

namespace Osm\Admin\Base\Attributes\Route;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Interface_
{
    public function __construct(public string $class_name)
    { }
}