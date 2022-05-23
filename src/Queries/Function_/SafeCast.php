<?php

namespace Osm\Admin\Queries\Function_;

use Osm\Admin\Queries\Exceptions\InvalidCall;
use Osm\Admin\Queries\Function_;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Type;
use Osm\Admin\Queries\Formula;
use function Osm\__;

#[Type('safe_cast')]
class SafeCast extends Function_
{
    public function resolve(Formula\Call $call, Table $table): void {
        $this->argCountIs($call, 3);
        $this->argIsTypedLiteral($call, 1, 'string');

        $dataType = $call->args[1]->value();

        if (!isset($this->data_types[$dataType])) {
            throw new InvalidCall(
                __("Pass a valid data type name to the 2-nd argument of the ':function' function", [
                    'function' => strtoupper($call->type),
                ]),
                $call->formula, $call->pos, $call->length);
        }

        $call->data_type = $this->data_types[$dataType];
        $call->array = false;
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        return $call->data_type->safeCastToSql($call->args[0], $call->args[2],
            $bindings, $from, $join);
    }

}