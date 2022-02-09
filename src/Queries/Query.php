<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Table $table
 * @property ?int $limit
 * @property ?int $offset
 * @property bool $hydrate
 */
class Query extends Object_
{
    /**
     * @var Formula[]
     */
    public array $filters = [];

    /**
     * @var Formula[]
     */
    public array $selects = [];

    /**
     * @var Formula[]
     */
    public array $orders = [];

    /**
     * @var Query[]
     */
    public array $child_queries = [];

    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    public function where(string|array $formula, mixed ...$args): static {
        throw new NotImplemented($this);
    }

    public function select(string|array ...$formulas): static {
        foreach ($formulas as $formula) {
            $this->selects[$formula] = $this->parse($formula);
        }

        return $this;
    }

    public function orderBy(string|array $formula, bool $desc = false): static {
        throw new NotImplemented($this);
    }

    public function limit(?int $limit): static {
        throw new NotImplemented($this);
    }

    public function offset(?int $offset): static {
        throw new NotImplemented($this);
    }

    public function hydrate(bool $hydrate = false): static {
        throw new NotImplemented($this);
    }

    public function query(string $propertyName, callable $callback): static {
        throw new NotImplemented($this);
    }

    /**
     * @param string|array ...$formulas
     * @return \stdClass[]|Object_[]|array
     */
    public function get(string|array ...$formulas): array {
        throw new NotImplemented($this);
    }

    public function first(string|array ...$formulas): \stdClass|Object_|null {
        $this
            ->select(...$formulas)
            ->offset(0)
            ->limit(1);

        foreach ($this->get() as $object) {
            return $object;
        }

        return null;
    }

    public function value(string|array $formula): mixed {
        if (($value = $this->first($formula)) === null) {
            return null;
        }

        throw new NotImplemented($this);
    }

    public function chunk(callable $callback, int $size = 100): void {
        throw new NotImplemented($this);
    }

    public function count(): int {
        $query = static::new([
            'table' => $this->table,
            'filters' => $this->filters,
        ]);

        return $query->value("COUNT()");
    }

    public function insert(\stdClass|array $data): int {
        throw new NotImplemented($this);
    }

    public function update(\stdClass|array $data): void {
        throw new NotImplemented($this);
    }

    public function delete(): void {
        throw new NotImplemented($this);
    }

    protected function parse(array|string $formula): Formula
    {
        $parameters = [];
        if (is_array($formula)) {
            foreach ($formula as $key => $value) {
                $parameters = $value;
                $formula = $key;
                break;
            }
        }

        return Parser::new(['text' => $formula, 'parameters' => $parameters])
            ->parse()
            ->resolve($this->table);
    }

}