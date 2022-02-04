<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Db\Db;
use function Osm\dehydrate;
use function Osm\hydrate;

/**
 * @property Class_[] $classes #[Serialized]
 * @property Class_\Table[] $tables
 * @property Db $db
 */
class Schema extends Object_
{
    protected function get_classes(): array {
        throw new NotImplemented($this);
    }

    protected function get_tables(): array {
        return array_filter($this->classes,
            fn (Class_ $class) => $class->type == 'table');
    }

    public function __wakeup(): void {
        foreach ($this->classes as $class) {
            $class->schema = $this;
        }
    }

    public function migrate(): void {
        $current = null;

        if ($json = $this->db->table('schema')->value('current')) {
            $current = hydrate(Schema::class, json_decode($json));
        }

        //$this->migrateDown($current);
        $this->migrateUp($current);
//        $this->migrateIndexers();
//        $this->seed($current);

        $this->db->table('schema')->update([
            'current' => json_encode(dehydrate($this)),
        ]);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function migrateUp(?Schema $current): void
    {
        foreach ($this->tables as $table) {
            $currentTable = $current->tables[$table->name] ?? null;
            if ($currentTable) {
                $table->alter($currentTable);
            }
            else {
                $table->create();
            }
        }
    }
}