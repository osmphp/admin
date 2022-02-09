<?php

namespace Osm\Admin\Ui\Attributes;

use Osm\Admin\Schema\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Grid extends Attribute
{
    public array $columns;

    public function __construct(array ...$columns)
    {
        $this->columns = $columns;
    }
}