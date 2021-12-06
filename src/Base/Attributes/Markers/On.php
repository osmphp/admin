<?php

namespace Osm\Admin\Base\Attributes\Markers;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class On
{
    public function __construct(public string $type)
    {
    }
}