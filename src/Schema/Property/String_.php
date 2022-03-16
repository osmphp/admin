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
 * @property ?int $max_length #[Serialized]
 * @property string $size #[Serialized]
 *
 * @uses Serialized
 */
#[Type('string')]
class String_ extends Scalar
{
    const VARCHAR_LENGTH = 255;

    protected function get_max_length(): ?int {
        return null;
    }

    protected function get_size(): string {
        return static::SMALL;
    }

    public function create(Blueprint $table): void {
        if ($this->max_length && $this->max_length <= static::VARCHAR_LENGTH) {
            $column = $table->string($this->name, $this->max_length);
        }
        else {
            $column = match ($this->size) {
                static::TINY => $table->tinyText($this->name),
                static::SMALL => $table->text($this->name),
                static::MEDIUM => $table->mediumText($this->name),
                static::LONG => $table->longText($this->name),
            };
        }

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }

    public function createIndex(SearchBlueprint $index): Field
    {
        return $index->string($this->name);
    }

    protected function get_index_faceted(): bool
    {
        if (!$this->index_filterable) {
            return false;
        }

        if ($this->option_class_name) {
            return true;
        }

        return false;
    }

    public function parseUrlFilter(Query $query, string $operator,
                                   string|array|bool $value): void
    {
        if (is_bool($value)) {
            // string properties ignore URL flags
            return;
        }

        if ($this->option_class_name) {
            $this->parseUrlOptionFilter($query, $operator, $value);
        }
    }

    protected function parseUrlOptionFilter(Query $query,
        string $operator, string|array $values): void
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

                if (!isset($this->options[$option])) {
                    continue;
                }

                $items[] = $this->options[$option]->value;
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
}