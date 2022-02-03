<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class After
{
    public array $property_names;

    public function __construct(array ...$propertyNames)
    {
        $this->property_names = $propertyNames;
    }
}