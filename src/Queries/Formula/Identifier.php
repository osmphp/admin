<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Exceptions\InvalidIdentifier;
use Osm\Admin\Queries\Exceptions\InvalidQuery;
use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property string[] $parts #[Serialized]
 *
 * Resolved properties:
 *
 * @property Property[] $properties #[Serialized]
 * @property string[]|bool[] $from #[Serialized]
 * @property string $alias #[Serialized]
 * @property string $column #[Serialized]
 * @property ?string $path #[Serialized]
 * @property Property $property #[Serialized]
 *
 * @uses Serialized
 */
class Identifier extends Formula
{
    public $type = self::IDENTIFIER;

    protected function get_parts(): array {
        throw new Required(__METHOD__);
    }

    protected function get_properties(): array {
        throw new Required(__METHOD__);
    }

    protected function get_from(): array {
        throw new Required(__METHOD__);
    }

    protected function get_alias(): string {
        throw new Required(__METHOD__);
    }

    protected function get_column(): string {
        throw new Required(__METHOD__);
    }

    protected function get_path(): ?string {
        throw new Required(__METHOD__);
    }

    public function resolve(Table $table): void
    {
        $this->properties = [];
        $this->from = [$table->table_name => true];
        $this->alias = $table->table_name;
        $this->column = null;
        $this->path = null;
        $parent = $table;

        foreach ($this->parts as $i => $part) {
            $exceptionDetails = [
                'property' => $part,
                'parent' => implode('.',
                    array_slice($this->parts, 0, $i)),
            ];

            if (!$parent) {
                throw new InvalidIdentifier(__(
                    "Trying to retrieve ':property' of ':parent', but ':parent' is neither a record nor an object",
                    $exceptionDetails
                ));
            }

            if (!isset($parent->properties[$part])) {
                if ($i == 0 && count($this->parts) > 1 &&
                    ($singleton = $parent->schema->singletons[$part] ?? null))
                {
                    $parent = $singleton;
                    $this->from = [$singleton->table_name => true];
                    $this->alias = $singleton->table_name;
                    continue;
                }

                match ($parent->type) {
                    'table' => throw new InvalidIdentifier(__(
                        "':parent' record doesn't have ':property' property",
                        $exceptionDetails)),
                    'class' => throw new InvalidIdentifier(__(
                        "':parent' object doesn't have ':property' property",
                        $exceptionDetails)),
                    default => throw new NotSupported(__(
                        "Struct type ':type' not supported",
                        ['type' => $parent->type])),
                };
            }

            $this->properties[] = $property = $parent->properties[$part];
            $parent = $property instanceof Property\Bag
                ? $property->ref
                : null;

            if ($this->column && $property->explicit) {
                // earlier in this identifier, a parent object property
                // has already told what column the identifier should
                // resolve to. Having an explicit column inside anything
                // else than a table makes no sense
                throw new InvalidIdentifier(__(
                    "':property' property can't be explicit as ':parent' is not a record",
                    $exceptionDetails));
            }

            $property->resolve($this);
        }
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        foreach ($this->from as $alias => $joinClause) {
            if (isset($from[$alias])) {
                continue;
            }

            $from[$alias] = $joinClause === true
                ? true
                : "{$join} {$joinClause}";
        }

        $sql = "`{$this->alias}`.`{$this->column}`";

        if ($this->path) {
            $sql .= "->>'{$this->path}'";
        }

        return $sql;
    }

    protected function get_property(): Property {
        return $this->properties[count($this->properties) - 1];
    }
}