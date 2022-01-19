<?php

namespace Osm\Admin\Filters;

use Osm\Admin\Filters\Hints\AppliedFilters;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Core\App;
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
    const NOT_EQUALS = 'not_equals';

    public function parse(string $operator, bool|string|array $value)
        : ?array
    {
        return match ($operator) {
            static::EQUALS,
            static::NOT_EQUALS => $this->parse_equals($value),
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
            static::EQUALS,
            static::NOT_EQUALS =>
                $this->apply_equals($query, $operator, $values),
            default => throw new NotSupported(),
        };
    }

    protected function parse_equals(bool|string|array $value): ?array {
        throw new NotImplemented($this);
    }

    /**
     * @param Query $query
     * @param string $operator
     * @param AppliedFilter[] $values
     * @return void
     */
    protected function apply_equals(Query $query, string $operator,
        array $values): void
    {
        throw new NotImplemented($this);
    }

    protected function get_supports(): array {
        return [static::EQUALS, static::NOT_EQUALS];
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

    public function url(\stdClass|AppliedFilters $appliedFilters): string {
        return match ($appliedFilters->operator) {
            static::EQUALS,
            static::NOT_EQUALS => $this->url_equals($appliedFilters),
            default => throw new NotSupported(),
        };
    }

    public function url_equals(\stdClass|AppliedFilters $appliedFilters): string {
        throw new NotImplemented($this);
    }

    protected function operatorUrl(string $operator): string {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $module */
        $module = $osm_app->modules[Module::class];

        return array_search($operator, $module->operators);
    }
}