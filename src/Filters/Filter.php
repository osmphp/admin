<?php

namespace Osm\Admin\Filters;

use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_ $class
 * @property string $name #[Serialized]
 * @property string[] $supports #[Serialized]
 * @property Property $property
 */
class Filter extends Object_
{
    use SubTypes;

    const EQUALS = 'equals';

    public function parse(string $operator, bool|string|array $value)
        : ?array
    {
        return match ($operator) {
            static::EQUALS => $this->parse_equals($value),
            default => throw new NotSupported(),
        };
    }

    /**
     * @param Query $query
     * @param string $operator
     * @param AppliedFilter[] $values
     * @return void
     */
    public function apply(Query $query, string $operator, array $values)
        : void
    {
        match ($operator) {
            static::EQUALS => $this->apply_equals($query, $values),
            default => throw new NotSupported(),
        };
    }

    protected function parse_equals(bool|string|array $value): ?array {
        throw new NotImplemented($this);
    }

    /**
     * @param Query $query
     * @param AppliedFilter[] $values
     * @return void
     */
    protected function apply_equals(Query $query, array $values): void {
        throw new NotImplemented($this);
    }

    protected function get_supports(): array {
        return [static::EQUALS];
    }

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_name(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_property(): Property {
        return $this->class->properties[$this->name];
    }
}