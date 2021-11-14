<?php

namespace Osm\Admin\Storages\Traits;

use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use function Osm\dehydrate;
use function Osm\hydrate;

#[UseIn(Schema::class)]
trait SchemaTrait
{
    public function migrate(): void {
        /* @var Schema|static $this */
        global $osm_app; /* @var App $osm_app */

        /* @var Schema $current */
        $current = null;
        $db = $osm_app->db;

        if ($json = $db->table('global_')->value('schema')) {
            $current = hydrate(Schema::class, json_decode($json));
        }

        $this->migrateDown($current);
        $this->migrateUp($current);
        $this->seed($current);

        $db->table('global_')->update([
            'schema' => json_encode(dehydrate($this)),
        ]);
    }

    protected function migrateUp(?Schema $current): void {
        /* @var Schema|static $this */
        foreach ($this->classes as $class) {
            if (!$class->storage) {
                continue;
            }

            $currentStorage = $current->classes[$class->name]->storage ?? null;
            if ($currentStorage) {
                $class->storage->alter($currentStorage);
            }
            else {
                $class->storage->create();
            }
        }
    }

    protected function migrateDown(?Schema $current): void {
        if (!$current) {
            return;
        }

        /* @var Schema|static $this */
        foreach ($current->classes as $class) {
            if ($class->storage && !isset($this->classes[$class->name]->storage))
            {
                $class->storage->drop();
            }
        }
    }

    protected function seed(?Schema $current): void {
        /* @var Schema|static $this */
        foreach ($this->classes as $class) {
            if (!$class->storage) {
                continue;
            }

            $currentStorage = $current->classes[$class->name]->storage ?? null;
            $class->storage->seed($currentStorage);
        }
    }
}