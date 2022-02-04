<?php

namespace Osm\Admin\Ui\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Before
{
    public array $property_names;

    public function __construct(array ...$propertyNames)
    {
        $this->property_names = $propertyNames;
    }
}