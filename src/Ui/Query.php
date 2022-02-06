<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Queries\Query as DbQuery;
use Osm\Admin\Schema\Class_\Table;
use Osm\Admin\Ui\Exceptions\InvalidQuery;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use function Osm\__;
use function Osm\query;

/**
 * @property Table $table
 * @property DbQuery $query
 * @property bool $query_count
 * @property int $count
 */
class Query extends Object_
{
    protected bool $executed = false;

    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_query(): DbQuery {
        return query($this->table->name);
    }

    public function count(): static {
        $this->query_count = true;

        return $this;
    }

    protected function get_count(): int {
        $this->run();

        if (!isset($this->count)) {
            throw new InvalidQuery(__(
                "To retrieve record count, use `count()` method before accessing query results"));
        }

        return $this->count;
    }

    protected function run(): void
    {
        if ($this->executed) {
            return;
        }

        if ($this->query_count) {
            $this->count = $this->query->count();
        }
    }
}