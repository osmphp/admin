<?php

namespace Osm\Admin\Base\Attributes\Form;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Field
{
    public function __construct(public string $type)
    {
    }
}