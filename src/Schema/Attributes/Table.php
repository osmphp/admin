<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Table extends Attribute
{
    public function __construct(public string $name)
    {
    }
}