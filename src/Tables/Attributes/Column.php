<?php

namespace Osm\Data\Tables\Attributes;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Schema\Property;

abstract class Column
{
    public function getMethod(): string {
        return strtolower((new \ReflectionClass(static::class))
            ->getShortName());
    }

    public function create(Blueprint $table, Property $property,
        string $prefix): void
    {
        throw new NotImplemented($this);
    }

    public function createKey(Blueprint $table, Property $property): void
    {
    }


    public function createScope(Blueprint $table, Property $property,
        string $prefix): void
    {
        $this->create($table, $property, $prefix);
    }
}