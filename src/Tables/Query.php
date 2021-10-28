<?php

namespace Osm\Data\Tables;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Data\Queries\Query as BaseQuery;
use Osm\Data\Queries\Result;
use Osm\Data\Tables\Attributes\Table;
use Osm\Framework\Db\Db;

/**
 * @property Db $db
 * @property string $name
 */
class Query extends BaseQuery
{
    protected function run(): Result
    {
        // TODO: temporary implementation
        return Result::new([
            'items' => $this->db->table($this->name)->get()->toArray(),
        ]);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        /* @var Table $table */
        return ($table = $this->object_class->reflection
            ->attributes[Table::class] ?? null)
                ? $table->name
                : throw new Required(__METHOD__);
    }
}