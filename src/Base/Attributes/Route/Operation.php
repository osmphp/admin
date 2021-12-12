<?php

namespace Osm\Admin\Base\Attributes\Route;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Operation
{
    public function __construct(public string $name)
    { }
}