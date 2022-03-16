<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Ui\Exceptions\InvalidQuery;
use Osm\Core\Object_;
use function Osm\__;

/**
 * @property int $count
 * @property array $facets
 * @property \stdClass[] $items
 */
class Result extends Object_
{
    protected function get_count(): int {
        throw new InvalidQuery(__(
            "To retrieve record count, use `count()` method before accessing query results"));
    }

    protected function get_items(): array {
        throw new InvalidQuery(__(
            "To retrieve record data, use `items()` method before accessing query results"));
    }

    protected function get_facets(): array {
        throw new InvalidQuery(__(
            "To retrieve faceted data, use `facets()` method before accessing query results"));
    }
}