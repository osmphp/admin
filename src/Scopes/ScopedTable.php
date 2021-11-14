<?php

namespace Osm\Admin\Scopes;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Storages\Storage;
use Osm\Admin\Tables\Interfaces\HasColumns;
use Osm\Admin\Tables\Traits\Columns;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Framework\Db\Db;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $name #[Serialized]
 * @property Db $db
 */
#[Type('scoped_table')]
class ScopedTable extends Storage implements HasColumns
{
    use Columns;

    public function __wakeup(): void {
        $this->wakeupColumns();
    }

    public function create(): void {
        $this->db->create($this->name, function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('scope_id')->nullable();
            $table->foreign('scope_id')
                ->references('id')->on('scopes')
                ->onDelete('cascade');
        });
    }

    public function drop(): void
    {
        $this->db->drop($this->name);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }
}