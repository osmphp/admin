<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Queries\Exceptions\InvalidCall;
use Osm\Admin\Schema\DataType;
use Osm\Admin\Schema\Table;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;
use function Osm\__;

/**
 * Dependencies:
 *
 * @property DataType[] $data_types
 */
class Function_ extends Object_
{
    use SubTypes;

    public function resolve(Formula\Call $call, Table $table): void {
        throw new NotImplemented($this);
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        throw new NotImplemented($this);
    }

    protected function get_data_types(): array {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[\Osm\Admin\Schema\Module::class]->data_types;
    }

    protected function argCountIs(Formula\Call $call, int $count): void {
        if (count($call->args) !== $count) {
            throw new InvalidCall(
                __("Pass 2 arguments to the ':function' function", [
                    'function' => strtoupper($call->type),
                ]),
                $call->formula, $call->pos, $call->length);
        }
    }

    protected function argIsTypedLiteral(Formula\Call $call, int $arg,
                                         string       $dataType): void
    {
        if ($call->args[$arg]->type !== Formula::LITERAL ||
            $call->args[$arg]->data_type->type !== $dataType)
        {
            throw new InvalidCall(
                __("Pass a ':data_type' literal to the argument #:arg of the ':function' function", [
                    'function' => strtoupper($call->type),
                    'arg' => $arg + 1,
                    'data_type' => $dataType,
                ]),
                $call->formula, $call->pos, $call->length);
        }
    }

    protected function argsToSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        $sql = '';

        foreach ($call->args as $arg) {
            if ($sql) {
                $sql .= ', ';
            }

            $sql .= $arg->toSql($bindings, $from, $join);
        }

        return $sql;
    }
}