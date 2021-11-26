<?php

namespace Osm\Admin\Queries;

use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Query $query
 */
class Filter extends Object_
{
    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }
}