<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\dehydrate;
use function Osm\hydrate;

/**
 * @property Class_[] $classes #[Serialized]
 * @property Class_\Table[] $tables
 * @property Db $db
 * @property Descendants $descendants
 */
class Schema extends Object_
{
    protected function get_classes(): array {
        throw new Required(__METHOD__);
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

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }

    public function parse(): static {
        $this->classes = [];

        foreach ($this->descendants->classes(Record::class) as
            $reflection)
        {
            $this->parseClass($reflection);
        }

        foreach ($this->classes as $class) {
            $class->parse();
        }

        return $this;
    }

    protected function parseClass(CoreClass $reflection): void {
        global $osm_app; /* @var App $osm_app */

        if (isset($this->classes[$reflection->name])) {
            return;
        }

        $data = [
            'schema' => $this,
            'name' => $reflection->name,
            'reflection' => $reflection,
        ];

        if ($reflection->parent_class_name === Record::class) {
            $class = Class_\Table::new($data);
        }
        elseif ($reflection->parent_class_name === Object_::class) {
            $class = Class_\Struct::new($data);
        }
        elseif (isset($reflection->attributes[Type::class])) {
            $class = Class_\Type::new($data);
        }
        else {
            $class = Class_::new($data);
        }

        $this->classes[$reflection->name] = $class;

        foreach ($reflection->properties as $property) {
            if ($property->class_name !== $reflection->name) {
                continue;
            }

            if ($referencedReflection = $osm_app->classes[$property->type]
                    ?? null)
            {
                $this->parseClass($referencedReflection);
            }
        }
    }
}