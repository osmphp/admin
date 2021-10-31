<?php

namespace Osm\Data\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Data\Queries\Query;
use Osm\Data\Queries\Result;
use Osm\Data\Queries\Traits\Dehydrated;
use Osm\Data\Tables\Attributes\Table as TableAttribute;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property Db $db
 * @property string $name
 */
class Table extends Query
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
        /* @var TableAttribute $table */
        return ($table = $this->object_class->reflection
            ->attributes[TableAttribute::class] ?? null)
                ? $table->name
                : throw new Required(__METHOD__);
    }

    protected function applySelect(QueryBuilder $query): void
    {
        if (empty($this->select)) {
            throw new NotImplemented($this);
        }

        foreach (array_keys($this->select) as $propertyName) {
            if (!($property = $this->object_class->properties[$propertyName]
                ?? null))
            {
                throw new NotImplemented($this);
            }

            if ($property->column) {
                $query->addSelect($property->name);
            }
            elseif (!in_array('data', $query->columns)) {
                $query->addSelect('data');
            }
        }
    }

    public function insert(\stdClass|Object_ $data): ?int {
        if (!$this->dehydrated) {
            throw new NotImplemented($this);
        }

        if (!($data instanceof \stdClass)) {
            throw new NotImplemented($this);
        }

        return $this->doInsert($this->dehydratedInsertValues($data));
    }

    protected function doInsert(array $values): ?int
    {
        return $this->db->table($this->name)->insertGetId($values);
    }

    protected function dehydratedInsertValues(\stdClass $data): array {
        $values = [];

        foreach ($this->object_class->properties as $property) {
            if ($property->column && isset($data->{$property->name})) {
                $values[$property->name] = $data->{$property->name};
                unset($data->{$property->name});
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