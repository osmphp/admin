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
 * @property string[] $notify_updates_with
 * @property string[] $notify_deletes_with
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

    public function updatesData(array|\stdClass $data): bool {
        foreach ($data as $propertyName => $value) {
            if (!isset($this->storage->columns[$propertyName])) {
                return true;
            }
        }

        return false;
    }

    /**
     * If a property that is stored in `data` column is going to be updated,
     * selects all properties stored in `data` column.
     *
     * @param array|\stdClass $data
     * @return $this
     */
    public function selectData(array|\stdClass $data): static {
        if (!$this->updatesData($data)) {
            return $this;
        }

        foreach ($this->class->properties as $property) {
            if (!isset($this->storage->columns[$property->name])) {
                $this->select($property->name);
            }
        }

        return $this;
    }

    public function mergeData(\stdClass $data, \stdClass $current): \stdClass {
        foreach ($this->class->properties as $property) {
            if (isset($this->storage->columns[$property->name])) {
                continue;
            }

            if (property_exists($data, $property->name)) {
                continue;
            }

            if (isset($current->{$property->name})) {
                $data->{$property->name} = $current->{$property->name};
            }
        }

        return $data;
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
        $this->db_query = $this->db->table($this->name);

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
        $this->db_query = $this->db->table($this->name);

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
            $data->id = $this->db->table($this->name)->insertGetId(
                $this->save($data));

            $this->inserted($data);

            $this->db->committed(function () use ($data) {
                $this->insertCommitted($data);
            });

            return $data->id;
        });
    }

    protected function inserting(\stdClass $data): void {
    }

    protected function inserted(\stdClass $data): void {
        foreach ($this->storage->notifies as $event) {
            if ($event->notify_inserted) {
                $event->trigger($data);
            }
        }
    }

    protected function insertCommitted(\stdClass $data): void {
        $this->indexing->index();
    }

    protected function save(\stdClass $data): array {
        $data = (array)$data;
        $values = [];

        unset($data['id']);

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

        if (!empty($data)) {
            $values['data'] = json_encode((object)$data);
        }

        return $values;
    }

    public function doUpdate(\stdClass|array $data): void {
        $this->db_query->update($this->aliased($this->save($data)));
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
                $this->doUpdate($data);
                $this->batchUpdated($data);
                return;
            }

            $this->select(...$this->notify_updates_with)->orderBy('id');

            $this->chunk(function (\stdClass $item) use ($data) {
                $data = merge($item, $data);
                $this->updating($data);

                $this->db->table($this->name)
                    ->where('id', $data->id)
                    ->update($this->save($data));

                $this->updated($data);
            });
        });
    }

    protected function batchUpdating(\stdClass $data): bool {
        return false;
    }

    protected function batchUpdated(\stdClass $data): void {
    }

    protected function updating(\stdClass $data): void {
    }

    protected function updated(\stdClass $data): void {
        foreach ($this->storage->notifies as $event) {
            if ($event->notify_updated) {
                $event->trigger($data);
            }
        }
    }

    protected function updateCommitted(\stdClass $data): void {
        $this->indexing->index();
    }

    protected function aliased(array $values): array {
        if (empty($this->joins)) {
            return $values;
        }

        $aliased = [];

        foreach ($values as $key => $value) {
            $aliased["{$this->name}.{$key}"] = $value;
        }

        return $aliased;
    }

    protected function get_notify_updates_with(): array {
        $notifyWith = ['id'];

        foreach ($this->storage->notifies as $event) {
            if ($event->notify_updated) {
                $notifyWith = array_merge($notifyWith, $event->notified_with);
            }
        }

        return array_unique($notifyWith);
    }

    protected function get_notify_deletes_with(): array {
        $notifyWith = ['id'];

        foreach ($this->storage->notifies as $event) {
            if ($event->notify_deleting) {
                $notifyWith = array_merge($notifyWith, $event->notified_with);
            }
        }

        return array_unique($notifyWith);
    }

    public function delete(): void {
        $this->db->transaction(function() {
            $this->db->committed(function () {
                $this->deleteCommitted();
            });

            $this->select(...$this->notify_deletes_with)->orderBy('id');

            $this->chunk(function (\stdClass $data) {
                $this->deleting($data);

                $this->db->table($this->name)
                    ->where('id', $data->id)
                    ->delete();

                $this->deleted($data);
            });
        });
    }

    protected function deleting(\stdClass $data): void {
        foreach ($this->storage->notifies as $event) {
            if ($event->notify_deleting) {
                $event->trigger($data);
            }
        }
    }

    protected function deleted(\stdClass $data): void {
    }

    protected function deleteCommitted(): void {
        $this->indexing->index();
    }
}