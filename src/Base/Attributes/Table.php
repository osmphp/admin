<?php

namespace Osm\Admin\Base\Attributes;

use Osm\Admin\Base\Attributes\Markers\Object_;

#[\Attribute(\Attribute::TARGET_CLASS), Object_]
final class Table
{
    public function __construct(public string $name)
    {
    }
}