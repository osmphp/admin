<?php

namespace Osm\Admin\Base\Attributes\Markers\Grid;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Column
{
    public function __construct(public string $type)
    {
    }
}