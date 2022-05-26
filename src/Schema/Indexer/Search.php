<?php

namespace Osm\Admin\Schema\Indexer;

use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Indexer;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Search\Blueprint;
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

    protected function get_listens_to(): array {
        return [
            $this->table->name => [
                Query::INSERTED => 'inserts',
                Query::UPDATED => 'updates',
                Query::DELETED => 'deletes',
            ],
        ];
    }

    public function index(string $mode): void {
        if ($mode == static::FULL) {
            $this->fullReindex();
        }
        else {
            $this->partialReindex();
        }
    }

    protected function fullReindex(): void {
        if ($this->search->exists($this->table->table_name)) {
            $this->search->drop($this->table->table_name);
        }

        $this->search->create($this->table->table_name, function(Blueprint $index) {
            foreach ($this->table->properties as $property) {
                if ($property->name === 'id') {
                    continue;
                }

                if ($property->index) {
                    $field = $property->createIndex($index);

                    if ($property->index_filterable) {
                        $field->filterable();
                    }

                    if ($property->index_sortable) {
                        $field->sortable();
                    }

                    if ($property->index_searchable) {
                        $field->searchable();
                    }

                    if ($property->index_faceted) {
                        $field->faceted();
                    }
                }
            }
        });

        // TODO: implement and use `chunk()` method, and insert in bulks
        foreach ($this->query()->get() as $item) {
            $this->searchQuery()->insert((array)$item);
        }
    }

    protected function partialReindex(): void {
        $listensTo = $this->listens_to[$this->table->name];

        // copy new entries
        $query = $this->query()->joinInsertNotifications($this);
        foreach ($query->get() as $item) {
            $this->searchQuery()->insert((array)$item);
        }

        // delete processed insert notifications
        $notificationTable = $this->getNotificationTableName($this->table,
            $listensTo[Query::INSERTED]);
        $this->db->table($notificationTable)->delete();

        // update existing entries
        $query = $this->query()->joinUpdateNotifications($this);
        foreach ($query->get() as $item) {
            $this->searchQuery()->update($item->id, (array)$item);
        }

        // delete processed update notifications
        $notificationTable = $this->getNotificationTableName($this->table,
            $listensTo[Query::UPDATED]);
        $this->db->table($notificationTable)->delete();

        // delete removed entries
        $notificationTable = $this->getNotificationTableName($this->table,
            $listensTo[Query::DELETED]);
        foreach ($this->db->table($notificationTable)->pluck('id') as $id) {
            $this->searchQuery()->delete($id);
        }

        // delete processed delete notifications
        $this->db->table($notificationTable)->delete();
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