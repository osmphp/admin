<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Exceptions\UnknownFunction;
use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Function_;
use Osm\Admin\Queries\Module;
use Osm\Admin\Schema\Table;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property string $function_name #[Serialized]
 * @property Formula[] $args #[Serialized]
 *
 * @property Function_[] $functions
 *
 * Resolved properties:
 *
 * @property Function_ $function #[Serialized]
 * @property ?string $alias #[Serialized] COLUMN function remembers the main
 *      table alias
 * @property ?string $column_name #[Serialized] COLUMN function remembers
 *      the column name passed as an argument
 *
 * @uses Serialized
 */
class Call extends Formula
{
    public $type = self::CALL;

    protected function get_function_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_args(): array {
        throw new Required(__METHOD__);
    }

    protected function get_function(): Function_ {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->args as $arg) {
            $arg->parent = $this;
        }
    }

    public function resolve(Table $table): void
    {
        $this->function = $this->functions[$this->function_name] ?? null;

        if (!$this->function) {
            throw new UnknownFunction(__("Unknown function ':function'", [
                'function' => $this->function_name,
            ]));
        }

        foreach ($this->args as $arg) {
            $arg->resolve($table);
        }

        $this->function->resolve($this, $table);
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        return $this->function->toSql($this, $bindings, $from, $join);
    }

    protected function get_functions(): array {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $module */
        $module = $osm_app->modules[Module::class];

        return $module->functions;
    }
}