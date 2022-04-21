<?php

namespace Osm\Admin\Schema;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Exceptions\InvalidRename;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Search\Blueprint as SearchBlueprint;
use Osm\Framework\Search\Search;
use function Osm\__;

/**
 * @property string $table_name #[Serialized]
 * @property string[] $select_identifiers #[Serialized]
 * @property bool $singleton #[Serialized]
 * @property Db $db
 * @property Search $search
 * @property string[] $after #[Serialized]
 * @property Indexer[] $indexers
 * @property Indexer[] $listeners
 *
 * @uses Serialized
 */
#[Type('table')]
class Table extends Struct
{
    public const SCHEMA_PROPERTY = 'tables';
    public const ROOT_CLASS_NAME = Record::class;

    protected function get_table_name(): string {
        return str_replace(' ', '_', $this->s_objects_lowercase);
    }

    protected function get_select_identifiers(): array {
        return ['title'];
    }

    protected function get_singleton(): bool {
        return false;
    }

    public function create(): void
    {
        $this->db->create($this->table_name, function(Blueprint $table) {
            foreach ($this->properties as $property) {
                if ($property->explicit) {
                    $property->create($table);
                }
            }

            $table->json('_data')->nullable();
            $table->json('_overrides')->nullable();
        });

        if ($this->search->exists($this->table_name)) {
            $this->search->drop($this->table_name);
        }

        $this->search->create($this->table_name, function(SearchBlueprint $index) {
            foreach ($this->properties as $property) {
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

        foreach ($this->listeners as $listener) {
            $listener->createNotificationTables($this);
        }
    }

    public function alter(\stdClass|Table $current): void
    {
        throw new NotImplemented($this);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_search(): Search {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->search;
    }

    protected function get_after(): array {
        $after = [];

        foreach ($this->properties as $property) {
            if ($property->type !== 'record') {
                continue;
            }

            if (!$property->explicit) {
                continue;
            }

            if ($property->ref_class_name !== $this->name) {
                $after[] = $property->ref_class_name;
            }
        }

        return $after;
    }

    protected function get_indexers(): array {
        return [
            'search' => Indexer\Search::new(),
        ];
    }

    protected function get_listeners(): array {
        return array_map(fn(string $name) => $this->schema->indexers[$name],
            $this->schema->listener_names[$this->name]);
    }

    public function diff(Migrator\Table $table): void {
        foreach ($this->properties as $property) {
            $property->diff($table->property($property));
        }

        if ($table->old) {
            foreach ($table->old->properties as $property) {
                $this->planDeletingProperty($table, $property);
            }
        }
    }

    protected function planDeletingProperty(Migrator\Table $table,
        Property $property): void
    {
        throw new NotImplemented($this);
    }
}