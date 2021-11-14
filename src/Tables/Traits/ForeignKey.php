<?php

namespace Osm\Admin\Tables\Traits;
use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $references #[Serialized]
 * @property ?string $references_table #[Serialized]
 * @property ?string $references_column #[Serialized]
 * @property ?string $on_delete #[Serialized]
 */
trait ForeignKey
{
    protected function get_references_table(): ?string {
        return $this->references
            ? substr($this->references, 0,
                strpos($this->references, '.'))
            : null;
    }

    protected function get_references_column(): ?string {
        return $this->references
            ? substr($this->references,
                strpos($this->references, '.') + 1)
            : null;
    }

    protected function createForeignKey(Blueprint $table): void {
        if ($this->references) {
            $foreign = $table->foreign("{$this->property->name}")
                ->references($this->references_column)
                ->on($this->references_table);

            if ($this->on_delete) {
                $foreign->onDelete($this->on_delete);
            }
        }
    }
}