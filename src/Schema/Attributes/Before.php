<?php

namespace Osm\Admin\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Before
{
    public array $property_names;

    public function __construct(array ...$propertyNames)
    {
        $this->property_names = $propertyNames;
    }
}