<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Indexing\Index;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Queries\Query;
use Osm\Admin\Queries\Result;
use Osm\Admin\Queries\Traits\Dehydrated;
use Osm\Admin\Base\Attributes\Table as TableAttribute;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property Table $storage
 * @property Db $db
 * @property string $name
 *
 * @property \stdClass $data
 * @property string[] $data_after_insert
 * @property array $insert_values
 * @property array $update_after_insert_values
 */
class TableQuery extends Query
{
    use Dehydrated;

    protected function run(): Result
    {
        // TODO: temporary implementation
        $query = $this->db->table($this->name);

        $this->applySelect($query);

        return Result::new([
            'items' => $query->get()
                ->map(fn(\stdClass $item) => $this->load($item))
                ->toArray(),
        ]);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        return $this->class->storage->name;
    }

    protected function applySelect(QueryBuilder $query): void
    {
        if (empty($this->select)) {
            throw new NotImplemented($this);
        }

        foreach (array_keys($this->select) as $propertyName) {
            if (!($property = $this->class->properties[$propertyName]
                ?? null))
            {
                throw new NotImplemented($this);
            }

            if ($this->storage->columns[$propertyName]) {
                $query->addSelect($property->name);
            }
            elseif (!in_array('data', $query->columns)) {
                $query->addSelect('data');
            }
        }
    }

    public function insert(\stdClass $data): ?int {
        $this->data = clone $data;
        $this->inserting();

        return $this->db->transaction(function() use ($data) {
            $this->data->id = $this->db->table($this->name)
                ->insertGetId($this->insert_values);

            $this->data_after_insert = [];
            $this->inserted();
            if (!empty($this->data_after_insert)) {
                $this->db->table($this->name)
                    ->where('id', $this->data->id)
                    ->update($this->update_after_insert_values);
            }

            $this->db->committed(function () {
                $this->insertCommitted();
            });

            return $this->data->id;
        });
    }

    protected function inserting(): void {
        $this->index?->inserting($this);
    }

    protected function inserted(): void {
        $this->index?->inserted($this);
    }

    protected function insertCommitted(): void {
    }

    protected function get_insert_values(): array {
        $data = (array)$this->data;
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

    protected function get_update_after_insert_values(): array {
        $data = (array)$this->data;
        $values = [];

        foreach ($data as $propertyName => $value) {
            if (!isset($this->data_after_insert[$propertyName])) {
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

            if (!empty($this->insert_values['data'])) {
                $data = merge(json_decode($this->insert_values['data']), $data);
            }

            $values['data'] = json_encode($data);
        }

        return $values;
    }

    protected function load(\stdClass $item): \stdClass|Object_
    {
        if (!$this->dehydrated) {
            throw new NotImplemented($this);
        }

        if (isset($item->data)) {
            $item = merge($item, json_decode($item->data));
        }

        foreach ($item as $property => $value) {
            if (!isset($this->select[$property])) {
                unset($item->$property);
            }
        }

        return $item;
    }
}