<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Core\App;
use Osm\Admin\Queries\Query;
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

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        return $this->class->storage->name;
    }

    public function get(...$formulas): array {
        $this->select(...$formulas);
        $this->prepareSelect();

        return $this->db_query->get([])
            ->map(fn(\stdClass $item) => $this->load($item))
            ->toArray();
    }

    public function first(...$formulas): \stdClass|Object_|null
    {
        $this->select(...$formulas);
        $this->prepareSelect();

        $item = $this->db_query->first();
        return $item ? $this->load($item) : null;
    }

    protected function prepareSelect(): void
    {
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
                $values = $this->insertValues($data));

            $modified = [];
            $this->inserted($data, $modified);

            if (!empty($modified)) {
                $this->db->table($this->name)
                    ->where('id', $data->id)
                    ->update($this->updateAfterInsertValues($data, $values,
                        $modified));
            }

            $this->db->committed(function () use ($data) {
                $this->insertCommitted($data);
            });

            return $data->id;
        });
    }

    protected function inserting(\stdClass $data): void {
        $this->index?->inserting($this, $data);
    }

    protected function inserted(\stdClass $data, &$modified): void {
        $this->index?->inserted($this, $data, $modified);
    }

    protected function insertCommitted(\stdClass $data): void {
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

    protected function updateAfterInsertValues(\stdClass $data,
        array $insertValues, array $modified): array
    {
        $data = (array)$data;
        $values = [];

        foreach ($data as $propertyName => $value) {
            if (!isset($modified[$propertyName])) {
                unset($data[$propertyName]);
                continue;
            }

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
            $data = (object)$data;
            if (!empty($insertValues['data'])) {
                $data = merge(json_decode($insertValues['data']), $data);
            }

            $values['data'] = json_encode($data);
        }

        return $values;
    }
}