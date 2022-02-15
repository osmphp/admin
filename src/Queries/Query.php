<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Queries\Exceptions\InvalidQuery;
use Osm\Admin\Schema\Table;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Db\Db;
use function Osm\__;

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
     * @var Formula\SelectExpr[]
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

    public static array $operators = [
        Parser::OR_ => " OR ",
        Parser::AND_ => " AND ",
        Parser::EQ => " = ",
        Parser::GT_EQ => " >= ",
        Parser::GT => " > ",
        Parser::LT_EQ => " <= ",
        Parser::LT => " < ",
        Parser::LT_GT => " <> ",
        Parser::NOT_EQ => " <> ",
        Parser::PLUS => " + ",
        Parser::MINUS => " - ",
    ];

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
        $this->select(...$formulas);

        $bindings = [];
        $sql = $this->generateSelect($bindings);

        return array_map(fn(\stdClass $item) => $this->load($item),
            $this->db->connection->select($sql, $bindings));
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
        if (($item = $this->first($formula)) === null) {
            return null;
        }

        foreach ($item as $value) {
            return $value;
        }

        return null;
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

    public function insert(array $data): int {
        $bindings = [];
        $sql = $this->generateInsert($data, $bindings);

        $this->db->connection->insert($sql, $bindings);
        return (int)$this->db->connection->getPdo()->lastInsertId();
    }

    public function update(array $data): void {
        // don't do anything if there are no properties to update
        if (empty($data)) {
            return;
        }

        $bindings = [];
        $sql = $this->generateUpdate($data, $bindings);
        $this->db->connection->update($sql, $bindings);
    }

    public function delete(): void {
        $bindings = [];
        $sql = $this->generateDelete($bindings);
        $this->db->connection->delete($sql, $bindings);
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

        $parsed = Parser::new(['text' => $formula, 'parameters' => $parameters])
            ->parse($as);

        $parsed->resolve($this->table);

        return $parsed;
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function generateSelect(array &$bindings): string
    {
        $from = [$this->table->table_name => true];
        $where = $this->generateWhere($bindings, $from);
        $select = $this->generateSelects($bindings, $from);
        $orderBy = $this->generateOrderBy($bindings, $from);

        return <<<EOT
{$select}
FROM {$this->generateFrom($from)}
{$where}
{$orderBy}
{$this->generateLimit()}
{$this->generateOffset()}
EOT;
    }

    protected function generateInsert(array $data, array &$bindings): string {
        list($columns, $values) = $this->generateInserts($data, $bindings);

        return <<<EOT
INSERT INTO `{$this->table->table_name}` 
({$columns})
VALUES ({$values})
EOT;
    }

    protected function generateUpdate(array $data, array &$bindings): string {
        $from = [$this->table->table_name => true];
        $updates = $this->generateUpdates($data, $bindings);
        $where = $this->generateWhere($bindings, $from);

        return <<<EOT
UPDATE {$this->generateFrom($from)}
SET {$updates}
{$where}
EOT;
    }

    protected function generateDelete(array &$bindings): string {
        $from = [$this->table->table_name => true];
        $where = $this->generateWhere($bindings, $from);

        return <<<EOT
DELETE FROM {$this->generateFrom($from)}
{$where}
EOT;
    }

    protected function generateSelects(array &$bindings, array &$from): string
    {
        $sql = '';

        if (empty($this->selects)) {
            throw new InvalidQuery(__("Add a select expression to the query."));
        }

        foreach ($this->selects as $formula) {
            if ($sql) {
                $sql .= ', ';
            }

            $sql .= $formula->toSql($bindings, $from, 'LEFT OUTER');
        }

        return "SELECT {$sql}";
    }

    protected function generateFrom(array $from): string
    {
        $sql = '';

        ksort($from);

        foreach ($from as $alias => $on) {
            if ($on !== true) { // if it's a JOIN
                $sql .= "\n    {$on}";
                continue;
            }

            // otherwise, it's the main table, or a singleton
            if ($sql) {
                $sql .= ", \nFROM ";
            }

            $sql .= "`{$alias}`";
        }

        return $sql;
    }

    protected function generateWhere(array &$bindings, array &$from): string
    {
        $sql = '';

        if (empty($this->filters)) {
            return $sql;
        }

        foreach ($this->filters as $formula) {
            if ($sql) {
                $sql .= ' AND ';
            }

            $sql .= '(' . $formula->toSql($bindings, $from, 'INNER') .
                ')';
        }

        return "WHERE {$sql}";
    }

    protected function generateOrderBy(array &$bindings, array &$from): string
    {
        $sql = '';

        if (empty($this->orders)) {
            return $sql;
        }

        foreach ($this->orders as $formula) {
            if ($sql) {
                $sql .= ', ';
            }

            $sql .= $formula->toSql($bindings, $from, 'LEFT OUTER');
        }

        return "ORDER BY {$sql}";
    }

    protected function generateLimit(): string
    {
        return $this->limit !== null
            ? "LIMIT {$this->limit}"
            : '';
    }

    protected function generateOffset(): string
    {
        return $this->offset !== null
            ? "OFFSET {$this->offset}"
            : '';
    }

    protected function load(\stdClass $item): \stdClass|Object_
    {
        foreach ($this->selects as $formula) {
            $value = $item->{$formula->alias};
            if ($value === null) {
                unset($item->{$formula->alias});
                continue;
            }

            if ($formula->array) {
                throw new NotImplemented($this);
            }

            switch ($formula->data_type) {
                case 'int':
                    $item->{$formula->alias} = (int)$value;
                    break;
                case 'bool':
                    $item->{$formula->alias} = (bool)$value;
                    break;
                case 'float':
                    $item->{$formula->alias} = (float)$value;
                    break;
                case 'object':
                    $item->{$formula->alias} = json_decode($value);
                    break;
            }
        }

        return $item;
    }

    protected function generateUpdates(array $data, array &$bindings)
        : string
    {
        $updates = [];
        $sql = '';

        foreach ($data as $propertyName => $value) {
            $property = $this->table->properties[$propertyName];
            $property->update($updates, $value);
        }

        foreach ($updates as $columnName => $update) {
            list($updateSql, $updateBindings) = $update;

            if ($sql) {
                $sql .= ', ';
            }

            $sql .= "`$columnName` = $updateSql";
            $bindings = array_merge($bindings, $updateBindings);
        }

        return $sql;
    }

    protected function generateInserts(array $data, array &$bindings): array
    {
        $inserts = [];

        foreach ($data as $propertyName => $value) {
            $property = $this->table->properties[$propertyName];
            $property->insert($inserts, $value);
        }

        $columns = '';
        $values = '';
        foreach ($inserts as $columnName => $value) {
            if ($columns) {
                $columns .= ', ';
                $values .= ', ';
            }

            if ($columnName == 'data') {
                $value = json_encode($value);
            }

            $columns .= "`$columnName`";
            $values .= '?';
            $bindings[] = $value;
        }
        return [$columns, $values];
    }
}