<?php

namespace Osm\Admin\Base\Attributes\Grid;

use Osm\Admin\Base\Attributes\Markers\Grid\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('primary_key')]
final class PrimaryKey
{
    public function __construct(public string $title)
    {
    }
}