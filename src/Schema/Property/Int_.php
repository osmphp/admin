<?php

namespace Osm\Admin\Schema\Property;
use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Ui\Query;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Search\Blueprint as SearchBlueprint;
use Osm\Framework\Search\Field;

/**
 * @property bool $unsigned #[Serialized]
 * @property string $size #[Serialized]
 * @property bool $auto_increment #[Serialized]
 *
 * @uses Serialized
 */
#[Type('int')]
class Int_ extends Scalar
{
    protected function get_unsigned(): bool {
        return false;
    }

    protected function get_size(): string {
        return static::MEDIUM;
    }

    protected function get_auto_increment(): bool {
        return false;
    }

    public function parseUrlFilter(Query $query, string $operator,
                                   string|array|bool $value): void
    {
        if (is_bool($value)) {
            // string properties ignore URL flags
            return;
        }

        if ($this->name == 'id') {
            $this->parseIdFilter($query, $operator, $value);
        }
    }

    protected function parseIdFilter(Query $query, string $operator,
        array|string $values): void
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $items = [];

        foreach ($values as $value) {
            foreach (explode(' ', $value) as $option) {
                if ($option === '') {
                    continue;
                }

                if (($option = filter_var($option, FILTER_VALIDATE_INT))
                    === false)
                {
                    continue;
                }

                $items[] = $option;
            }
        }

        if (empty($items)) {
            return;
        }

        switch($operator) {
            case '': $query->whereIn($this->name, $items); break;
            case '-': $query->whereNotIn($this->name, $items); break;
        }
    }

    public function createIndex(SearchBlueprint $index): Field
    {
        return $index->int($this->name);
    }
}