<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Attributes\Markers\Table\Column as ColumnMarker;
use Osm\Admin\Queries\Query;
use Osm\Admin\Storages\Storage;
use Osm\Admin\Tables\Interfaces\HasColumns;
use Osm\Admin\Tables\Traits\Columns;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;

/**
 * @property string $name #[Serialized]
 * @property Db $db
 */
#[Type('table')]
class Table extends Storage implements HasColumns
{
    use Columns;

    public function __wakeup(): void {
        $this->wakeupColumns();
    }

    public function create(): void {
        $this->db->create($this->name, function(Blueprint $table) {
            foreach ($this->columns as $column) {
                $column->create($table);
            }
            $table->json('data')->nullable();
        });
    }

    public function alter(Storage $current): void {
        $this->alterDown($current);
        $this->alterUp($current);
    }

    public function drop(): void {
        $this->db->drop($this->name);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function alterDown(Storage|Table $current): void
    {
        foreach ($current->columns as $column) {
            if (!isset($this->columns[$column->name])) {
                $this->db->alter($this->name, function (Blueprint $table)
                    use ($column)
                {
                    $column->drop($table);
                });
            }
        }
    }

    protected function alterUp(Storage|Table $current): void
    {
        foreach ($this->columns as $column) {
            if (!isset($current->columns[$column->name])) {
                $this->db->alter($this->name, function (Blueprint $table)
                    use ($column)
                {
                    $column->create($table);
                });
            }
        }
    }

    protected function genericQuery(): Query
    {
        return TableQuery::new(['storage' => $this]);
    }
}