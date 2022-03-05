<?php

namespace Osm\Admin\Ui\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class View
{
    public function __construct(public string $name)
    {
    }
}