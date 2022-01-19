<?php

namespace Osm\Admin\Filters\Filter;

use Osm\Admin\Filters\Filter;
use Osm\Admin\Filters\AppliedFilter;
use Osm\Admin\Filters\Hints\AppliedFilters;
use Osm\Admin\Queries\Query;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;

#[Type('id')]
class Id extends Filter
{
    protected function parse_equals(bool|string|array $value): ?array {
        $appliedFilters = [];

        if (is_bool($value)) {
            return $appliedFilters;
        }

        $arguments = is_array($value) ? $value : [$value];

        foreach ($arguments as $values) {
            foreach (explode(' ', $values) as $value) {
                if ($value === '') {
                    continue;
                }

                if (!is_numeric($value)) {
                    continue;
                }

                $appliedFilters[$value] = AppliedFilter\Id::new([
                    'filter' => $this,
                    'value' => (int)$value,
                ]);
            }
        }

        return array_values($appliedFilters);
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
        switch ($operator) {
            case static::EQUALS:
                if (count($values) === 1) {
                    $query->equals($this->name, $values[0]->value);
                }
                else {
                    $query->in($this->name,
                        array_map(fn($value) => $value->value, $values));
                }
                break;
            case static::NOT_EQUALS:
                if (count($values) === 1) {
                    $query->notEquals($this->name, $values[0]->value);
                }
                else {
                    $query->notIn($this->name,
                        array_map(fn($value) => $value->value, $values));
                }
                break;
            default:
                throw new NotSupported();
        }
    }

    public function url_equals(\stdClass|AppliedFilters $appliedFilters): string {
        return "{$this->name}{$this->operatorUrl($appliedFilters->operator)}=" .
            implode('+', array_map(
                fn(AppliedFilter\Id $value) => $value->value,
                $appliedFilters->values
            )
        );
    }
}