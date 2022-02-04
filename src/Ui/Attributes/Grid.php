<?php

namespace Osm\Admin\Ui\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Grid
{
    public array $columns;

    public function __construct(array ...$columns)
    {
        $this->columns = $columns;
    }
}