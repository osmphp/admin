<?php

namespace Osm\Admin\Indexing;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Attributes\Indexer\From;
use Osm\Admin\Base\Attributes\Indexer\To;
use Osm\Admin\Base\Attributes\Markers\IndexerSource;
use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Cache;
use Osm\Framework\Db\Db;

/**
 * @property Indexer[] $indexers #[Cached('indexers')]
 * @property int[] $indexer_source_ids #[Cached('indexer_source_ids')]
 * @property Db $db
 * @property Cache $cache
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];

    protected function get_indexer_source_ids(): array {
        $items = $this->db->table('indexer_sources')
            ->get(['id', 'indexer', 'source']);

        $ids = [];

        foreach($items as $item) {
            $ids["{$item->indexer}|{$item->source}"] = $item->id;
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

            if (!empty($indexer->sources)) {
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
            $query = $this->db->table('indexer_sources');
            foreach ($query->pluck('id') as $id) {
                $this->migrateDown($id);
            }
        }

        $ids = [];
        foreach ($this->indexers as $indexer) {
            foreach ($indexer->sources as $source) {
                $id = $this->db->table('indexer_sources')
                    ->where('indexer', $indexer->__class->name)
                    ->where('source', $source->name)
                    ->value('id');

                if (!$id) {
                    $id = $this->migrateUp($source);
                }

                if ($id) {
                    $ids[] = $id;
                }
            }
        }

        $query = $this->db->table('indexer_sources');
        if (!empty($ids)) {
            $query->whereNotIn('id', $ids);
        }
        foreach ($query->pluck('id') as $id) {
            $this->migrateDown($id);
        }

        unset($this->indexers);
        $this->cache->deleteItem('indexers');
        unset($this->indexer_source_ids);
        $this->cache->deleteItem('indexer_source_ids');
    }

    protected function migrateUp(Source $source): ?int
    {
        if (!$this->db->exists($source->table)) {
            return null;
        }

        $source->id = $this->db->table('indexer_sources')->insertGetId([
            'indexer' => $source->indexer->__class->name,
            'source' => $source->name,
            'table' => $source->table,
        ]);

        $source->create();

        return $source->id;
    }

    protected function migrateDown(int $id): void
    {
        $this->db->dropIfExists("notifications__{$id}");
        $this->db->table('indexer_sources')
            ->where('id', $id)
            ->delete();
    }

    protected function sources(Class_ $class): array
    {
        throw new NotImplemented($this);
    }

    public function index(bool $incremental = true): void {
        throw new NotImplemented($this);
    }
}