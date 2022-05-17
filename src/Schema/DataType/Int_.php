<?php

namespace Osm\Admin\Schema\DataType;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

#[Type('int')]
class Int_ extends Scalar
{
    public function castToSql(Formula $formula, array &$bindings,
        array &$from, string $join): string
    {
        $sql = $formula->toSql($bindings, $from, $join);

        if ($formula->data_type->type === 'string') {
            return "0 + {$sql}";
        }

        throw new NotImplemented($this);
    }
}