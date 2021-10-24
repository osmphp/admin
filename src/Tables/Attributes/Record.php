<?php

namespace Osm\Data\Tables\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Record
{
    public function __construct(public string $class_name)
    {
    }
}