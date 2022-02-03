<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Virtual
{
    public function __construct(public ?string $formula = null)
    {
    }
}