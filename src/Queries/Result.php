<?php

namespace Osm\Data\Queries;

use Osm\Core\Object_;
use Osm\Framework\Search\Hints\Result\Facet;

/**
 * @property \stdClass[]|Object_[]|int[]|string[] $items
 * @property ?int $count
 * @property Facet[]|null $facets
 * @property \stdClass|Object_|int|string|null $first
 */
class Result extends Object_
{
    protected function get_first(): \stdClass|Object_|int|string|null {
        return $this->items[0] ?? null;
    }
}