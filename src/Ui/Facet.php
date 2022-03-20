<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Framework\Blade\View;
use Osm\Framework\Search\Query as SearchQuery;
use Osm\Framework\Search\Result as SearchResult;

/**
 * @property Property $property
 *
 * Render-time properties:
 *
 * @property Query $query
 * @property bool $visible
 */
class Facet extends View
{
    use RequiredSubTypes;

    public function query(SearchQuery $searchQuery): void {
        throw new NotImplemented($this);
    }

    public function populate(Query $query, SearchResult $result): mixed {
        throw new NotImplemented($this);
    }

    protected function get_visible(): bool {
        throw new NotImplemented($this);
    }

    public function prepare(): void {
        throw new NotImplemented($this);
    }
}