<?php

namespace Osm\Admin\Tables\Event;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Indexing\Event;
use Osm\Core\Attributes\Type;

#[Type('subtree_deleted')]
class SubtreeDeleted extends Event
{
    public bool $notify_deleting = true;

    public function create(): void {
        $this->db->create($this->notification_table, function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
        });
    }

    protected function handle(int $id, string $id_path): void {
        $this->db->table($this->notification_table)->insertOrIgnore([
            'id' => $id,
        ]);

        $query = $this->db->table($this->table)
            ->where('id_path', 'like', "{$id_path}/%");

        foreach ($query->pluck('id') as $id) {
            $this->db->table($this->notification_table)->insertOrIgnore([
                'id' => $id,
            ]);
        }

        $this->db->table('events')
            ->where('id', $this->id)
            ->update(['changed' => true]);
    }

    protected function clear(): void {
        $this->db->table($this->notification_table)->delete();
    }
}