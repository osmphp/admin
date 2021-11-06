<?php

namespace Osm\Admin\Base\Attributes\Grid;

use Osm\Admin\Base\Attributes\Markers\Grid\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('string')]
final class String_
{
    public function __construct(public string $title)
    {
    }
}