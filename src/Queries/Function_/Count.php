<?php

namespace Osm\Admin\Queries\Function_;

use Osm\Admin\Queries\Function_;
use Osm\Core\Attributes\Type;
use Osm\Admin\Queries\Formula;

#[Type('count')]
class Count extends Function_
{
    public function resolve(Formula\Call $call): void {
        $call->data_type = 'int';
        $call->array = false;
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        return "COUNT(*)";
    }
}