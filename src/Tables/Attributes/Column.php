<?php

namespace Osm\Data\Tables\Attributes;

abstract class Column
{
    public function getMethod(): string {
        return strtolower((new \ReflectionClass(static::class))
            ->getShortName());
    }
}