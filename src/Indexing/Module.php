<?php

namespace Osm\Admin\Indexing;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Attributes\On\Saved;
use Osm\Admin\Base\Attributes\On\Saving;
use Osm\Admin\Base\Attributes\Markers\On;
use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Cache;
use Osm\Framework\Db\Db;

/**
 * @property Indexer[] $indexers #[Cached('indexers')]
 * @property int[] $event_ids #[Cached('event_ids')]
 * @property Db $db
 * @property Cache $cache
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];

    protected bool $indexing = false;

    protected function get_event_ids(): array {
        $events = $this->db->table('events')
            ->get(['id', 'indexer', 'alias']);

        $ids = [];

        foreach($events as $event) {
            $ids["{$event->indexer}|{$event->alias}"] = $event->id;
        }

        return $ids;
    }

    protected function get_indexers(): array {
        global $osm_app; /* @var App $osm_app */

        $classes = $osm_app->descendants->classes(Indexer::class);
        $indexers = [];

        foreach ($classes as $class) {
            $new = "{$class->name}::new";
            /* @var Indexer $indexer */
            $indexer = $new([
                'name' => $class->name,
            ]);

            if (!empty($indexer->events)) {
                $indexers[$class->name] = $indexer;
            }
        }

        return $indexers;
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_cache(): Cache {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->cache;
    }

    public function migrate(bool $fresh = false): void {
        if ($fresh) {
            $query = $this->db->table('events');
            foreach ($query->pluck('id') as $id) {
                $this->migrateDown($id);
            }
        }

        $ids = [];
        foreach ($this->indexers as $indexer) {
            foreach ($indexer->events as $event) {
                $id = $this->db->table('events')
                    ->where('indexer', $indexer->__class->name)
                    ->where('alias', $event->alias)
                    ->value('id');

                if (!$id) {
                    $id = $this->migrateUp($event);
                }

                if ($id) {
                    $ids[] = $id;
                }
            }
        }

        $query = $this->db->table('events');
        if (!empty($ids)) {
            $query->whereNotIn('id', $ids);
        }
        foreach ($query->pluck('id') as $id) {
            $this->migrateDown($id);
        }

        unset($this->indexers);
        $this->cache->deleteItem('indexers');
        unset($this->event_ids);
        $this->cache->deleteItem('event_ids');
    }

    protected function migrateUp(Event $event): ?int
    {
        if (!$this->db->exists($event->table)) {
            return null;
        }

        $event->id = $this->db->table('events')->insertGetId([
            'indexer' => $event->indexer->__class->name,
            'alias' => $event->alias,
            'table' => $event->table,
        ]);

        $event->create();

        return $event->id;
    }

    protected function migrateDown(int $id): void
    {
        $this->db->dropIfExists("notifications__{$id}");
        $this->db->table('events')
            ->where('id', $id)
            ->delete();
    }

    public function index(bool $incremental = true): void {
        if ($this->indexing) {
            return;
        }

        $this->indexing = true;

        try {
            if (!$incremental) {
                $this->db->table('events')
                    ->update(['dirty' => true]);
            }

            foreach ($this->indexers as $indexer) {
                if ($indexer->dirty()) {
                    $this->db->transaction(function () use ($indexer) {
                        $indexer->index();
                        $indexer->clearDirtyFlag();
                        foreach ($indexer->events as $event) {
                            $event->clearChangedFlag();
                        }
                    });
                    continue;
                }

                foreach ($indexer->events as $event) {
                    if ($event->changed()) {
                        $this->db->transaction(function () use ($event) {
                            $event->indexer->index(event: $event);
                            $event->clearChangedFlag();
                        });
                    }
                }
            }
        }
        finally {
            $this->indexing = false;
        }
    }
}