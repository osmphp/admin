<?php

namespace Osm\Admin\Filters;

use Osm\Admin\Queries\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;

/**
 * @property Filter $filter
 */
class AppliedFilter extends Object_
{
    use SubTypes;

    protected function get_filter(): Filter {
        throw new Required(__METHOD__);
    }
}