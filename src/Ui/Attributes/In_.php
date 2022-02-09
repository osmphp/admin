<?php

namespace Osm\Admin\Ui\Attributes;

use Osm\Admin\Schema\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class In_ extends Attribute
{
    public function __construct(public string $fieldset_path)
    {
    }
}