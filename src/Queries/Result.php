<?php

namespace Osm\Data\Queries;

use Osm\Core\Object_;

/**
 * @property \stdClass[]|Object_[] $items
 * @property \stdClass|Object_|null $first
 */
class Result extends Object_
{
    protected function get_first(): \stdClass|Object_|null {
        return $this->items[0] ?? null;
    }
}