<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Schema\Table;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Db\Db;

/**
 * @property Table $table
 * @property ?int $limit
 * @property ?int $offset
 * @property bool $hydrate
 * @property Db $db
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
        $this->filters[] = $this->parse(empty($args)
            ? $formula
            : [$formula => $args]
        );

        return $this;
    }

    public function select(string|array ...$formulas): static {
        foreach ($formulas as $formula) {
            $this->selects[] = $this->parse($formula,
                Formula::SELECT_EXPR);
        }

        return $this;
    }

    public function orderBy(string|array $formula, bool $desc = false): static {
        $this->orders[] = $this->parse($formula, Formula::SORT_EXPR);

        return $this;
    }

    public function limit(?int $limit): static {
        $this->limit = $limit;

        return $this;
    }

    public function offset(?int $offset): static {
        $this->offset = $offset;

        return $this;
    }

    public function hydrate(bool $hydrate = false): static {
        $this->hydrate = $hydrate;

        return $this;
    }

    /**
     * @param string|array ...$formulas
     * @return \stdClass[]|Object_[]|array
     */
    public function get(string|array ...$formulas): array {
        $bindings = [];
        $sql = $this->generateSelect($bindings);

        return $this->db->connection->select($sql, $bindings);
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

    protected function parse(array|string $formula, string $as = Formula::EXPR)
        : Formula
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
            ->parse($as);
            //->resolve($this->table);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function generateSelect(array &$bindings): string
    {
        $sql = 'SELECT ';
        $sql .= $this->generateSelects($bindings);
        $sql .= $this->generateFrom($bindings);
        $sql .= $this->generateFilters($bindings);
        $sql .= $this->generateOrders($bindings);
        $sql .= $this->generateOffset($bindings);
        $sql .= $this->generateLimit($bindings);

        return $sql;
    }

    protected function generateSelects(array &$bindings): string
    {
        $sql = '';

        foreach ($this->selects as $formula) {
            if ($sql) {
                $sql .= ', ';
            }

            $sql .= $formula->toSql($bindings);
        }

        return $sql;
    }

    protected function generateFrom(array &$bindings): string
    {
        throw new NotImplemented($this);
    }

    protected function generateFilters(array &$bindings): string
    {
        throw new NotImplemented($this);
    }

    protected function generateOrders(array &$bindings): string
    {
        throw new NotImplemented($this);
    }

    protected function generateLimit(array &$bindings): string
    {
        throw new NotImplemented($this);
    }

    protected function generateOffset(array &$bindings): string
    {
        throw new NotImplemented($this);
    }
}