<?php

namespace Osm\Admin\Base\Attributes\Form;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Part
{
    public function __construct(public string $type)
    {
    }
}