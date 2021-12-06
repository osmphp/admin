<?php

namespace Osm\Admin\Scopes\Indexers;

use Osm\Admin\Base\Attributes\On;
use Osm\Admin\Indexing\Event;
use Osm\Admin\Indexing\Indexer;
use Osm\Core\App;
use Osm\Framework\Db\Db;

/**
 * @property Db $db
 */
#[On\TreeDeleted('scopes')]
class RemoveScopeTables extends Indexer
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function index(Event $event = null): void {
        global $osm_app; /* @var App $osm_app */

        if (!$event || !$event->id) {
            return;
        }

        $ids = $this->db->table($event->notification_table)->pluck('id');
        foreach ($ids as $id) {
            $osm_app->schema->migrateScopeDown($id);
        }
    }
}