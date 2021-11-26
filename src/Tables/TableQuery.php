<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Core\App;
use Osm\Admin\Queries\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property Table $storage
 * @property Db $db
 * @property string $name
 *
 * @property QueryBuilder $raw Execution-phase property.
 */
class TableQuery extends Query
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        return $this->class->storage->name;
    }

    protected function get_raw(): QueryBuilder {
        return $this->db->table($this->name);
    }

    public function raw(callable $callback): static {
        $callback($this->raw);

        return $this;
    }

    public function get(...$expressions): array {
        $this->select(...$expressions);

        foreach ($this->filters as $filter) {
            $filter->addToTableQuery();
        }

        foreach ($this->selects as $select) {
            $select->addToTableQuery();
        }

        foreach ($this->orders as $order) {
            $order->addToTableQuery();
        }

        return $this->raw->get([])
            ->map(fn(\stdClass $item) => $this->load($item))
            ->toArray();
    }

    protected function load(\stdClass $item): \stdClass {
        throw new NotImplemented($this);
    }

    public function insert(\stdClass|array $data): int {
        $data = is_array($data) ? (object)$data : clone $data;
        $this->inserting($data);

        return $this->db->transaction(function() use ($data) {
            $data->id = $this->raw->insertGetId(
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