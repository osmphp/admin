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
use Osm\Framework\Db\Db;

/**
 * @property Indexer[] $indexers #[Cached('indexers')]
 * @property Db $db
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];

    protected function get_indexers(): array {
        global $osm_app; /* @var App $osm_app */

        $classes = $osm_app->descendants->classes(Indexer::class);
        $indexers = [];

        foreach ($classes as $class) {
            /* @var To $target */
            if (!($target = $class->attributes[To::class] ?? null)) {
                continue;
            }

            $new = "{$class->name}::new";
            $indexers[$class->name] = $new([
                'name' => $class->name,
                'target' => $target->name,
                'target_type' => $target->type_name,
            ]);
        }

        return $indexers;
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
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
    }

    protected function migrateUp(Source $source): ?int
    {
        if (!$this->db->exists($source->table)) {
            return null;
        }

        $id = $this->db->table('indexer_sources')->insertGetId([
            'indexer' => $source->indexer->__class->name,
            'source' => $source->name,
            'table' => $source->table,
        ]);


        $this->db->create("notifications__{$id}",
            function (Blueprint $table) use ($source) {
                $source->createNotificationTable($table);
            }
        );

        return $id;
    }

    protected function migrateDown(int $id): void
    {
        $this->db->drop("notifications__{$id}");
        $this->db->table('indexer_sources')
            ->where('id', $id)
            ->delete();
    }

    protected function sources(Class_ $class): array
    {
    }
}