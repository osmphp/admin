<?php

namespace Osm\Admin\Queries;

use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Query $query
 * @property Expression $expression
 */
class Select extends Object_
{
    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }

    protected function get_expression(): Expression {
        throw new Required(__METHOD__);
    }
}