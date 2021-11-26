<?php

namespace Osm\Admin\Queries;

use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Query $query
 * @property Expression $expression
 * @property bool $desc
 */
class Order extends Object_
{
    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }
}