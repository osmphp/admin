<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Precision extends Attribute
{
    public function __construct(public int $precision)
    {
    }
}