<?php

namespace Osm\Admin\Scopes;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Traits\Id;
use Osm\Admin\Storages\Storage;
use Osm\Admin\Tables\Interfaces\HasColumns;
use Osm\Admin\Tables\Traits\Columns;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Framework\Db\Db;
use Osm\Core\Attributes\Serialized;
use function Osm\__;

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

    public function alter(Storage $current): void
    {
        throw new NotImplemented($this);
    }

    public function drop(): void
    {
        $this->db->drop($this->name);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function createScope(int $scopeId): void {
        if (!isset($this->columns['id'])) {
            throw new NotSupported(__("Use ':trait' trait in ':class' class.", [
                'trait' => Id::class,
                'class' => $this->class->name,
            ]));
        }

        $this->db->create("s{$scopeId}__{$this->name}", function(Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->foreign('id')
                ->references('id')->on($this->name)
                ->onDelete('cascade');

            foreach ($this->columns as $column) {
                if ($column->name !== 'id') {
                    $column->create($table);
                }
            }
            $table->json('data')->nullable();
        });
    }

    public function alterScope(int $scopeId, Storage $current): void {
        throw new NotImplemented($this);
    }

    public function dropScope(int $scopeId): void {
        $this->db->drop("s{$scopeId}__{$this->name}");
    }
}