<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Queries\Query as DbQuery;
use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Exceptions\InvalidQuery;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use function Osm\__;
use function Osm\query;

/**
 * @property Table $table
 * @property DbQuery $query
 * @property int $count
 * @property \stdClass[] $items
 */
class Query extends Object_
{
    protected bool $executed = false;
    protected bool $query_count = false;
    protected bool $query_items = true;
    protected bool $query_all = false;

    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_query(): DbQuery {
        return query($this->table->name);
    }

    public function count(bool $count = true): static {
        $this->query_count = $count;

        return $this;
    }

    public function items(bool $items = true): static {
        $this->query_items = $items;

        return $this;
    }

    public function all(bool $all = true): static {
        $this->query_all = $all;

        return $this;
    }

    public function url(array $url, string ...$ignore): static {
        //throw new NotImplemented($this);
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

    protected function get_items(): array {
        $this->run();

        if (!isset($this->items)) {
            throw new InvalidQuery(__(
                "To retrieve record data, use `items()` method before accessing query results"));
        }

        return $this->items;
    }

    protected function run(): void
    {
        if ($this->executed) {
            return;
        }

        if ($this->query_count) {
            $query = query($this->table->name, [
                'filters' => $this->query->filters,
            ]);
            $this->count = $query->value("COUNT() AS count");
        }

        if ($this->query_items) {
            $this->items = $this->query->get();
        }
    }
}