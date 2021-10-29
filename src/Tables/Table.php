<?php

namespace Osm\Data\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Data\Queries\Query;
use Osm\Data\Queries\Result;
use Osm\Data\Tables\Attributes\Table as TableAttribute;
use Osm\Framework\Db\Db;

/**
 * @property Db $db
 * @property string $name
 */
class Table extends Query
{
    protected function run(): Result
    {
        // TODO: temporary implementation
        $query = $this->db->table($this->name);

        $this->applySelect($query);

        return Result::new([
            'items' => $query->get()->toArray(),
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
}