<?php

namespace Osm\Admin\Tables\Event;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Indexing\Event;
use Osm\Core\Attributes\Type;

#[Type('saved')]
class Saved extends Event
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

        $this->db->table('events')
            ->where('id', $this->id)
            ->update(['changed' => true]);
    }

    protected function clear(): void {
        $this->db->table($this->notification_table)->delete();
    }
}