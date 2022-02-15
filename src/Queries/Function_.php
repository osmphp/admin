<?php

namespace Osm\Admin\Queries;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;

class Function_ extends Object_
{
    use SubTypes;

    public function resolve(Formula\Call $call): void {
        throw new NotImplemented($this);
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        throw new NotImplemented($this);
    }
}