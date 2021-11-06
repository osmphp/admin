<?php

namespace Osm\Admin\Base\Attributes\Markers\Form;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Chapter
{
    public function __construct(public string $type)
    {
    }
}