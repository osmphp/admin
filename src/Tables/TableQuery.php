<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Osm\Core\App;
use Osm\Admin\Queries\Query;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;
use Osm\Core\Object_;
use function Osm\hydrate;
use function Osm\merge;

/**
 * @property Table $storage
 * @property Db $db
 * @property string $name
 *
 * @property QueryBuilder $db_query Execution-phase property.
 */
class TableQuery extends Query
{
    public array $joins = [];

    /**
     * @var callable[]
     */
    public array $raw = [];

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        return $this->class->storage->name;
    }

    public function raw(callable $callback): static {
        $this->raw[] = $callback;

        return $this;
    }

    public function getRaw(...$formulas): Collection {
        $this->select(...$formulas);
        return $this->prepareSelect()->get([]);
    }

    public function get(...$formulas): array {
        return $this->getRaw(...$formulas)
            ->map(fn(\stdClass $item) => $this->load($item))
            ->toArray();
    }

    public function chunkRaw(callable $callback,
        int $size = self::DEFAULT_CHUNK_SIZE): void
    {
        $this->prepareSelect()->chunk($size, function($items) use ($callback) {
            foreach ($items as $item) {
                $callback($item);
            }
        });
    }

    public function chunk(callable $callback,
        int $size = self::DEFAULT_CHUNK_SIZE): void
    {
        $this->chunkRaw(function(\stdClass $item) use ($callback) {
            $callback($this->load($item));
        }, $size);
    }

    public function firstRaw(...$formulas): ?\stdClass
    {
        $this->select(...$formulas);
        return $this->prepareSelect()->first();
    }

    public function first(...$formulas): \stdClass|Object_|null
    {
        $item = $this->firstRaw(...$formulas);
        return $item ? $this->load($item) : null;
    }

    public function prepareSelect(): QueryBuilder {
        $this->db_query = $this->db->table("{$this->name} as this");

        foreach ($this->filters as $filter) {
            $filter->tables_filter($this, $this->db_query);
        }

        foreach ($this->selects as $select) {
            $select->tables_select($this);
        }

        foreach ($this->orders as $order) {
            $order->tables_order($this);
        }

        foreach ($this->raw as $callback) {
            $callback($this);
        }

        return $this->db_query;
    }

    protected function prepareBatch(): void
    {
        $this->db_query = $this->db->table("{$this->name} as this");

        foreach ($this->filters as $filter) {
            $filter->tables_filter($this, $this->db_query);
        }

        foreach ($this->raw as $callback) {
            $callback($this);
        }
    }

    protected function load(\stdClass $item): \stdClass|Object_ {
        foreach ((array)$item as $propertyName => $value) {
            if ($value === null) {
                unset($item->$propertyName);
            }
        }

        foreach (array_reverse(array_keys($this->joins)) as $alias) {
            foreach ((array)$item as $propertyName => $value) {
                if (!str_starts_with($propertyName, "{$alias}__")) {
                    continue;
                }

                $accessors = explode('__', $propertyName);
                $property = array_pop($accessors);
                $object = $item;
                foreach ($accessors as $accessor) {
                    if (!isset($object->$accessor)) {
                        $object->$accessor = new \stdClass();
                    }
                    $object = $object->$accessor;
                }
                $object->$property = $value;

                unset($item->$propertyName);
            }
        }

        return $this->hydrate
            ? hydrate($this->class->name, $item)
            : $item;
    }

    public function insert(\stdClass|array $data): int {
        $data = is_array($data) ? (object)$data : clone $data;
        $this->inserting($data);

        return $this->db->transaction(function() use ($data) {
            $id = $this->db->table($this->name)->insertGetId(
                $this->insertValues($data));

            $this->inserted($id, $data);

            $this->db->committed(function () use ($id, $data) {
                $this->insertCommitted($id, $data);
            });

            return $id;
        });
    }

    protected function inserting(\stdClass $data): void {
    }

    protected function inserted(int $id, \stdClass $data): void {
        foreach ($this->storage->indexer_sources as $source) {
            $source->inserted($id, $data);
        }
    }

    protected function insertCommitted(int $id, \stdClass $data): void {
        $this->indexing->index();
    }

    protected function insertValues(\stdClass $data): array {
        $data = (array)$data;
        $values = [];

        foreach ($data as $propertyName => $value) {
            if (!isset($this->class->properties[$propertyName])) {
                unset($data[$propertyName]);
            }
        }

        foreach ($this->storage->columns as $column) {
            if (isset($data[$column->name])) {
                $values[$column->name] = $data[$column->name];
                unset($data[$column->name]);
            }
        }

        $values['data'] = !empty($data) ? json_encode((object)$data) : null;

        return $values;
    }

    public function update(\stdClass|array $data): void {
        $this->db->transaction(function() use ($data) {
            if (is_array($data)) {
                $data = (object)$data;
            }

            $this->db->committed(function () use ($data) {
                $this->updateCommitted($data);
            });

            if ($this->batchUpdating($data)) {
                $this->prepareBatch();
                $this->db_query->update($this->updateValues($data));
                $this->batchUpdated($data);
                return;
            }

            $this->select('*')->orderBy('id');
            $modified = array_map(fn() => true, array_keys((array)$data));

            $this->chunk(function (\stdClass $item) use ($data, $modified) {
                $item = merge($item, $data);
                $this->updating($item, $modified);

                if (!empty($modified)) {
                    $this->db->table($this->name)
                        ->where('id', $item->id)
                        ->update($this->updateValues($item));
                }
                $this->updated($item);
            });
        });
    }

    protected function batchUpdating(\stdClass $data): bool {
        return false;
    }

    protected function batchUpdated(\stdClass $data): void {
    }

    protected function updating(\stdClass $data, array &$modified): void {
        $this->index?->updating($this, $data, $modified);
    }

    protected function updated(\stdClass $data): void {
    }

    protected function updateCommitted(\stdClass $data): void {
    }

    protected function updateValues(\stdClass $data): array {
        throw new NotImplemented($this);
    }
}