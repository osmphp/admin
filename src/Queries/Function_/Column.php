<?php

namespace Osm\Admin\Queries\Function_;

use Osm\Admin\Queries\Exceptions\InvalidCall;
use Osm\Admin\Queries\Function_;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Type;
use Osm\Admin\Queries\Formula;
use function Osm\__;

#[Type('column')]
class Column extends Function_
{
    public function resolve(Formula\Call $call, Table $table): void {
        $this->argCountIs($call, 2);
        $this->argIsTypedLiteral($call, 0, 'string');
        $this->argIsTypedLiteral($call, 1, 'string');

        $columnName = $call->args[0]->value();
        $dataType = $call->args[1]->value();

        if (!preg_match('/^[_a-zA-Z]\w*$/', $columnName)) {
            throw new InvalidCall(
                __("Pass a valid column name to the 1-st argument of the ':function' function", [
                    'function' => strtoupper($call->type),
                ]),
                $call->formula, $call->pos, $call->length);
        }

        if (!isset($this->data_types[$dataType])) {
            throw new InvalidCall(
                __("Pass a valid data type name to the 2-nd argument of the ':function' function", [
                    'function' => strtoupper($call->type),
                ]),
                $call->formula, $call->pos, $call->length);
        }

        $call->column_name = $columnName;
        $call->data_type = $this->data_types[$dataType];
        $call->array = false;
        $call->alias = $table->table_name;
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        return "`{$call->alias}`.`{$call->column_name}`";
    }

}