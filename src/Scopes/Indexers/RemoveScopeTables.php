<?php

namespace Osm\Admin\Scopes\Indexers;

use Osm\Admin\Base\Attributes\On;
use Osm\Admin\Indexing\Event;
use Osm\Admin\Tables\TableIndexer;
use Osm\Core\App;
use Osm\Framework\Db\Db;

/**
 * @property Db $db
 */
#[On\SubtreeDeleted('scopes')]
class RemoveScopeTables extends TableIndexer
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function index(int $id = null, Event $event = null): void {
        global $osm_app; /* @var App $osm_app */

        if ($id) {
            return;
        }

        if (!$event) {
            return;
        }

        $ids = $this->db->table($event->notification_table)->pluck('id');
        foreach ($ids as $id) {
            $osm_app->schema->migrateScopeDown($id);
        }
    }
}