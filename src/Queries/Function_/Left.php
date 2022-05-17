<?php

namespace Osm\Admin\Queries\Function_;

use Osm\Admin\Queries\Function_;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Type;
use Osm\Admin\Queries\Formula;

#[Type('left')]
class Left extends Function_
{
    public function resolve(Formula\Call $call, Table $table): void {
        $this->argCountIs($call, 2);
        $call->args[0] = $call->args[0]->castTo('string');
        $call->args[1] = $call->args[1]->castTo('int');

        $call->data_type = $this->data_types['string'];
        $call->array = false;
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        return "LEFT({$this->argsToSql($call, $bindings, $from, $join)})";
    }
}