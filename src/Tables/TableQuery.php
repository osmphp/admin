<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
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
        return $this->db->transaction(function() use ($data) {
            $id = $this->db->table($this->name)->insertGetId(
                $this->insertValues($data));

            $this->db->committed(function () use ($id) {
                $this->inserted($id);
            });

            return $id;
        });
    }

    protected function inserted(int $id): void {
    }

    protected function insertValues(\stdClass $data): array {
        $values = [];

        foreach ($this->storage->columns as $column) {
            if (isset($data->{$column->name})) {
                $values[$column->name] = $data->{$column->name};
                unset($data->{$column->name});
            }
        }

        $values['data'] = !empty($data) ? json_encode($data) : null;

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