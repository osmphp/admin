<?php

namespace Osm\Admin\Base\Attributes\Markers\Form;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Group
{
    public function __construct(public string $type)
    {
    }
}