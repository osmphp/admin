<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Queries\Query as DbQuery;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Framework\Search\Query as SearchQuery;

class Filter extends Object_
{
    use RequiredSubTypes;

    public function queryDb(DbQuery $query): void {
        throw new NotImplemented($this);
    }

    public function querySearch(SearchQuery $query): void {
        throw new NotImplemented($this);
    }
}