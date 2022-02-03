<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class In_
{
    public function __construct(public string $fieldset_path)
    {
    }
}