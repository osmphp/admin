<?php

namespace Osm\Admin\Schema\DataType;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

#[Type('string')]
class String_ extends Scalar
{
    public function castToSql(Formula $formula, array &$bindings,
                              array &$from, string $join): string
    {
        return "CONCAT({$formula->toSql($bindings, $from, $join)})";
    }
}