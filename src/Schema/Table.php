<?php

namespace Osm\Admin\Schema;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $table_name #[Serialized]
 * @property string[] $select_identifiers #[Serialized]
 * @property bool $singleton #[Serialized]
 * @property Db $db
 * @property string[] $after #[Serialized]
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
    }

    public function alter(Table $current): void
    {
        throw new NotImplemented($this);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
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
}