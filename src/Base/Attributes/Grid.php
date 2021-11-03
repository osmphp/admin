<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Grid
{
    public function __construct(public string $url)
    {
    }
}