<?php

namespace Osm\Admin\Indexing;

use Osm\Admin\Queries\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Index $index
 */
class Indexer extends Object_
{
    public function inserting(Query $query): void {
        throw new NotImplemented($this);
    }

    public function inserted(Query $query): void {
        throw new NotImplemented($this);
    }

    public function updating(Query $query, \stdClass $data): void {
        throw new NotImplemented($this);
    }

    protected function get_index(): Index {
        throw new Required(__METHOD__);
    }
}