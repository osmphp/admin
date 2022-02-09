<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\dehydrate;
use function Osm\hydrate;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_[] $classes #[Serialized]
 * @property Table[] $tables #[Serialized]
 * @property Db $db
 * @property Descendants $descendants
 *
 * @uses Serialized
 */
class Schema extends Object_
{
    protected function get_classes(): array {
        throw new Required(__METHOD__);
    }

    protected function get_tables(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void {
        foreach ($this->classes as $class) {
            $class->schema = $this;
        }

        foreach ($this->tables as $table) {
            $table->schema = $this;
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

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }

    public function parse(): static {
        global $osm_app; /* @var App $osm_app */

        $this->classes = [];
        $this->tables = [];

        $recordClass = $osm_app->classes[Record::class];

        foreach ($recordClass->child_class_names as $baseClassName) {
            $this->parseTable($osm_app->classes[$baseClassName]);
        }

        foreach ($this->tables as $table) {
            $table->parse();
        }

        foreach ($this->classes as $class) {
            $class->parse();
        }

        return $this;
    }

    protected function parseTable(CoreClass $reflection): void {
        if (isset($this->tables[$reflection->name])) {
            return;
        }

        $this->tables[$reflection->name] = $table = Table::new([
            'schema' => $this,
            'name' => $reflection->name,
            'reflection' => $reflection,
        ]);

        $this->parseProperties($table);
    }

    protected function parseClass(CoreClass $reflection): void {
        if (isset($this->classes[$reflection->name])) {
            return;
        }

        $this->classes[$reflection->name] = $class = Class_::new([
            'schema' => $this,
            'name' => $reflection->name,
            'reflection' => $reflection,
        ]);

        $this->parseProperties($class);
    }

    protected function parseProperties(Struct $struct): void {
        foreach ($struct->reflection->properties as $property) {
            if ($property->name != '__class') {
                $this->parseType($property->type);
            }
        }
    }

    protected function parseType(?string $type): void {
        global $osm_app; /* @var App $osm_app */

        if (!$type) {
            return;
        }

        if (!($class = $osm_app->classes[$type] ?? null)) {
            return;
        }

        for(; $class; $class = $class->parent_class) {
            if ($class->parent_class_name == Record::class) {
                $this->parseTable($class);
                break;
            }

            if ($class->parent_class_name == Object_::class) {
                $this->parseClass($class);
                break;
            }
        }
    }
}