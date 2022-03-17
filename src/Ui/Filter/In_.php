<?php

namespace Osm\Admin\Ui\Filter;

use Osm\Admin\Queries\Query as DbQuery;
use Osm\Admin\Ui\Filter;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Search\Hints\Result\Count;
use Osm\Framework\Search\Query as SearchQuery;

/**
 * @property Query $query
 * @property string $property_name
 * @property array $items
 * @property bool $not
 */
class In_ extends Filter
{
    protected function get_property_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_items(): array {
        throw new Required(__METHOD__);
    }

    protected function get_not(): bool {
        return false;
    }

    public function queryDb(DbQuery $query): void {
        throw new NotImplemented($this);
    }

    public function querySearch(SearchQuery $query): void {
        if ($this->not) {
            throw new NotImplemented($this);
        }

        $query->where($this->property_name, 'in', $this->items);
    }

    public function isOptionApplied(Count|\stdClass $option): bool {
        return in_array($option->value, $this->items);
    }
}