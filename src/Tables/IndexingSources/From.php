<?php

namespace Osm\Admin\Tables\IndexingSources;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Indexing\Source;
use Osm\Core\Attributes\Type;

#[Type('from')]
class From extends Source
{
    public bool $notify_inserted = true;
    public bool $notify_updated = true;

    public function create(): void {
        $this->db->create($this->notification_table, function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();

            $table->foreign('id')
                ->references('id')->on($this->table)
                ->onDelete('cascade');
        });
    }

    protected function handle(int $id): void {
        $this->db->table($this->notification_table)->insertOrIgnore([
            'id' => $id,
        ]);
    }
}