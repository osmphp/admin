<?php

namespace Osm\Admin\Base\Attributes\Grid;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column]
final class PrimaryKey
{
    public function __construct(public ?int $sort_order)
    {
    }
}