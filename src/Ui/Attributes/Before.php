<?php

namespace Osm\Admin\Ui\Attributes;

use Osm\Admin\Schema\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Before extends Attribute
{
    public array $property_names;

    public function __construct(array ...$propertyNames)
    {
        $this->property_names = $propertyNames;
    }
}