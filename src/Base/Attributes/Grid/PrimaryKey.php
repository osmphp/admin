<?php

namespace Osm\Admin\Base\Attributes\Grid;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('primary_key')]
final class PrimaryKey
{
    public function __construct(public string $title)
    {
    }
}