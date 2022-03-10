<?php

namespace Osm\Admin\Schema\Indexer;

use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Hints\IndexerStatus;
use Osm\Admin\Schema\Indexer;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Search\Query as SearchQuery;
use Osm\Framework\Search\Search as SearchEngine;
use function Osm\query;

/**
 * @property SearchEngine $search
 */
class Search extends Indexer
{
    protected function get_after_regexes(): array {
        return ['/__regular$/', '/__aggregate__$/'];
    }

    public function index(string $mode): void {
        if ($mode = static::FULL) {
            $this->fullReindex();
        }
        else {
            throw new NotImplemented($this);
        }
    }

    protected function fullReindex(): void {
        // TODO: use an API that clears all index entries with one call -
        // there should be one!
        foreach ($this->searchQuery()->ids() as $id) {
            $this->searchQuery()->delete($id);
        }

        // TODO: implement and use `chunk()` method, and insert in bulks
        foreach ($this->query()->get() as $item) {
            $this->searchQuery()->insert((array)$item);
        }
    }

    protected function get_search(): SearchEngine {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->search;
    }

    protected function query(): Query {
        $query = query($this->table->name)
            ->select('id');

        foreach ($this->table->properties as $property) {
            if ($property->index) {
                $query->select($property->name);
            }
        }

        return $query;
    }

    protected function searchQuery(): SearchQuery
    {
        return $this->search->index($this->table->table_name);
    }
}