<?php

namespace Osm\Admin\Schema\Class_;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type as TypeAttribute;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;

/**
 * @property string $table_name #[Serialized]
 * @property string[] $column_names #[Serialized]
 * @property Property[] $columns
 * @property Db $db
 */
#[TypeAttribute('table')]
class Table extends Class_
{
    protected function get_table_name(): string {
        return str_replace(' ', '_', $this->s_objects_lowercase);
    }

    protected function get_column_names(): array {
        return ['title'];
    }

    protected function get_columns(): array {
        throw new NotImplemented($this);
    }

    public function create(): void
    {
        $this->db->create($this->table_name, function(Blueprint $table) {
            $table->increments('id');
            $table->json('data')->nullable();
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
}