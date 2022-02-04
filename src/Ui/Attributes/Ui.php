<?php

namespace Osm\Admin\Ui\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Ui
{
    public function __construct(public string $area_class_name)
    {
    }
}