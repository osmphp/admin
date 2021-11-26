<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Base\Exceptions\SyntaxError;
use Osm\Admin\Base\Exceptions\UndefinedProperty;
use Osm\Admin\Indexing\Index;
use Osm\Admin\Storages\Storage;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Schema\Class_;
use function Osm\__;

/**
 * @property Storage $storage
 * @property Class_ $class
 * @property ?int $limit
 * @property ?Index $index
 * @property bool $hydrate
 */
class Query extends Object_
{
    /**
     * @var Filter[]
     */
    public array $filters = [];

    /**
     * @var Select[]
     */
    public array $selects = [];

    /**
     * @var Order[]
     */
    public array $orders = [];

    public const DEFAULT_CHUNK_SIZE = 100;
    public const DEFAULT_LIMIT = 10;

    protected function get_storage(): Storage {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        return $this->storage->class;
    }

    protected function get_limit(): ?int {
        return static::DEFAULT_LIMIT;
    }

    protected function get_index(): ?Index {
        return $this->storage->targeted_by[$this->data->type ?? ''] ?? null;
    }

    public function get(...$expressions): array {
        throw new NotImplemented($this);
    }

    public function first(...$expressions): \stdClass|Object_|null {
        throw new NotImplemented($this);
    }

    public function value(string $expression): mixed {
        if (($value = $this->first($expression)) === null) {
            return null;
        }

        foreach (array_keys($this->selects[$expression]->parsed_expression)
            as $propertyName)
        {
            if (($value = $value->$propertyName) === null) {
                return null;
            }
        }

        return $value;
    }

    public function insert(\stdClass|array $data): int {
        throw new NotImplemented($this);
    }

    public function update(\stdClass|array $data): void {
        throw new NotImplemented($this);
    }

    public function hydrate(): static {
        $this->hydrate = true;
        return $this;
    }

    public function raw(callable $callback): static {
        throw new NotImplemented($this);
    }

    public function select(...$expressions): static {
        foreach ($expressions as $text) {
            $this
                ->parseExpression($text, allowWildcard: true)
                ->select();
        }

        return $this;
    }

    public function parseExpression(string $text,
        bool $allowWildcard = false): Expression
    {
        $class = $this->class;
        $properties = [];
        $wildcard = false;

        foreach (explode('.', $text) as $propertyName) {
            if (!$class) {
                throw new SyntaxError(__("Can't use dot syntax after scalar property in ':expression' expression", [
                    'expression' => $text,
                ]));
            }

            if ($allowWildcard) {
                if ($wildcard) {
                    throw new SyntaxError(__("Can't use dot syntax after '*' in ':expression' expression", [
                        'expression' => $text,
                    ]));
                }

                if ($propertyName == '*') {
                    $wildcard = true;
                    continue;
                }
            }

            if (!($property = $class->properties[$propertyName] ?? null)) {
                throw new UndefinedProperty(__("':property' property, referenced in ':expression' expression, is not defined in ':class' class.", [
                    'property' => $propertyName,
                    'expression' => $text,
                    'class' => $class->name,
                ]));
            }

            $properties[$propertyName] = $property;
            $class = $class->schema->classes[$property->type] ?? null;
        }

        return $wildcard
            ? Expression\Wildcard::new([
                'query' => $this,
                'text' => $text,
                'properties' => $properties,
            ])
            : Expression\Identifier::new([
                'query' => $this,
                'text' => $text,
                'properties' => $properties,
            ]);
    }

    public function orderBy(string $expression, bool $desc = false): static {
        $this->orders[$expression] = Order::new([
            'query' => $this,
            'expression' => $this->parseExpression($expression),
            'desc' => $desc,
        ]);

        return $this;
    }

    public function equals(string $expression, mixed $value): static {
        $this->filters[] = Filter\Property\Equals::new([
            'query' => $this,
            'expression' => $this->parseExpression($expression),
            'value' => $value,
        ]);

        return $this;
    }

    public function and(callable $callback): static {
        $this->filters[] = $filter = Filter\Logical\And_::new([
            'query' => $this,
        ]);

        $callback($filter);

        return $this;
    }
}