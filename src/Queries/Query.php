<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Queries\Exceptions\InvalidQuery;
use Osm\Admin\Schema\Indexer;
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
    public const INSERTING = 'inserting';
    public const INSERTED = 'inserted';
    public const UPDATING = 'updating';
    public const UPDATED = 'updated';
    public const DELETING = 'deleting';
    public const DELETED = 'deleted';

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

    public array $notification_joins = [];

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
            $parsed = $this->parse($formula,
                Formula::SELECT_EXPR);

            $this->selects[$parsed->alias] = $parsed;
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

    public function joinInsertNotifications(Indexer $indexer,
        string $identifier = 'id'): static
    {
        return $this->joinNotifications($indexer, static::INSERTED,
            $identifier);
    }

    public function joinUpdateNotifications(Indexer $indexer,
        string $identifier = 'id'): static
    {
        return $this->joinNotifications($indexer, static::UPDATED,
            $identifier);
    }

    public function joinNotifications(Indexer $indexer, string $event,
        string $identifier): static
    {
        $this->notification_joins[] = [
            'identifier' => $this->parse($identifier, Formula::IDENTIFIER),
            'notification_table' => $indexer->getNotificationTableName(
                $this->table, $indexer->listens_to[$this->table->name][$event]),
        ];

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

    public function insert(array $data): int {
        // all input data is validated before running a transaction
        $this->validateProperties(static::INSERTING, $data);

        return $this->db->transaction(function() use($data) {
            // generate and execute SQL INSERT statement
            $bindings = [];
            $sql = $this->generateInsert($data, $bindings);

            $this->db->connection->insert($sql, $bindings);
            $data['id'] = $id =
                (int)$this->db->connection->getPdo()->lastInsertId();

            // compute regular, self and ID-based indexing expressions by
            // running an additional UPDATE. Note that property-level validation
            // rules on computed values are not executed - take care in formulas
            $this->computeProperties(static::INSERTED, $data);

            // register a callback that is executed after a successful transaction
            $this->db->committing(function() use ($data)
            {
                // validate modified objects as a whole, and their
                // dependent objects
                $this->validateObjects(static::INSERTED);

                // create notification records for the dependent objects in
                // other tables, and for search index entries
                $this->notifyListeners(static::INSERTED, $data);
            });

            // register a callback that is executed after a successful transaction
            $this->db->committed(function()
            {
                // successful transaction guarantees that current objects are
                // fully up-to-date (except aggregations), so it's a good time to
                // make sure that asynchronous indexing is queued, or to execute
                // it right away if queue is not configured. All types of asynchronous
                // indexing are queued/executed: regular, aggregation and search.
                $this->updateDependentObjects();
            });

            return $id;
        });
    }

    public function update(array $data): void {
        // don't do anything if there are no properties to update
        if (empty($data)) {
            // TODO: don't quit if there are computed properties
            return;
        }

        // all input data is validated before running a transaction
        $this->validateProperties(static::UPDATING, $data);

        $this->db->transaction(function() use($data) {
            // regular, self and ID-based indexing expressions are added
            // to the UPDATE statement. Note that property-level validation
            // rules on computed values are not executed - take care in formulas
            $this->computeProperties(static::UPDATING, $data);

            // generate and execute SQL UPDATE statement
            $bindings = [];
            $sql = $this->generateUpdate($data, $bindings);
            $this->db->connection->update($sql, $bindings);

            $this->db->committing(function() use ($data)
            {
                // validate modified objects as a whole, and their
                // dependent objects
                $this->validateObjects(static::UPDATED);

                // create notification records for the dependent objects in
                // other tables, and for search index entries
                $this->notifyListeners(static::UPDATED, $data);
            });

            // register a callback that is executed after a successful transaction
            $this->db->committed(function()
            {
                // successful transaction guarantees that current objects are
                // fully up-to-date (except aggregations), so it's a good time to
                //make sure that asynchronous indexing is queued, or to execute
                // it right away if queue is not configured. All types of asynchronous
                // indexing are queued/executed: regular, aggregation and search.
                $this->updateDependentObjects();
            });
        });
    }

    public function delete(): void {
        $this->db->transaction(function() {
            // generate and execute SQL DELETE statement
            $bindings = [];
            $sql = $this->generateDelete($bindings);
            $this->db->connection->delete($sql, $bindings);

            $this->db->committing(function()
            {
                // validate modified objects as a whole, and their
                // dependent objects
                $this->validateObjects(static::DELETED);

                // create notification records for the dependent objects in
                // other tables, and for search index entries
                $this->notifyListeners(static::DELETED);
            });

            // register a callback that is executed after a successful transaction
            $this->db->committed(function()
            {
                // successful transaction guarantees that current objects are
                // fully up-to-date (except aggregations), so it's a good time to
                //make sure that asynchronous indexing is queued, or to execute
                // it right away if queue is not configured. All types of asynchronous
                // indexing are queued/executed: regular, aggregation and search.
                $this->updateDependentObjects();
            });
        });
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
        $this->generateNotificationJoins($bindings, $from);

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

    protected function generateNotificationJoins(array &$bindings,
        array &$from): void
    {
        foreach ($this->notification_joins as $join) {
            $table = $join['notification_table'];
            /* @var Formula\Identifier $identifier */
            $identifier = $join['identifier'];

            $from[$table] = <<<EOT
INNER JOIN `$table`
        ON `$table`.`id` = {$identifier->toSql($bindings, $from, 'INNER')}
EOT;
        }
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

            switch ($formula->data_type->type) {
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

            if ($columnName == '_data') {
                $value = json_encode($value);
            }

            $columns .= "`$columnName`";
            $values .= '?';
            $bindings[] = $value;
        }
        return [$columns, $values];
    }

    protected function validateProperties(string $event, array $data): void {
        //throw new NotImplemented($this);
    }

    protected function validateObjects(string $event): void {
        //throw new NotImplemented($this);
    }

    protected function notifyListeners(string $event, array $data = []): void {
        foreach ($this->table->listeners as $listener) {
            $listener->notify($this, $event, $data);
        }
    }

    protected function updateDependentObjects(): void {
        $this->table->schema->index();
    }

    protected function computeProperties(string $event, array $data): void {
        //throw new NotImplemented($this);
    }

    public function clone(bool $where = false): static {
        $query = static::new(['table' => $this->table]);

        if ($where) {
            foreach ($this->filters as $formula) {
                $query->filters[] = $formula->clone();
            }
        }

        return $query;
    }

    public function intoNotificationTable(string $tableName): void {
        $bindings = [];
        $sql = "INSERT IGNORE INTO `{$tableName}` (`id`)\n";
        $sql .= $this->generateSelect($bindings);

        $this->db->connection->insert($sql, $bindings);
    }
}