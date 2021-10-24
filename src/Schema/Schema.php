<?php

namespace Osm\Data\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

/**
 * @property array $tables
 */
class Schema extends Object_
{
    protected function get_tables(): array {
        throw new NotImplemented($this);
    }
}