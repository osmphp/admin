<?php

namespace Osm\Data\Migrations\Traits\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\UseIn;
use Osm\Data\Migrations\Traits\ColumnTrait;
use Osm\Data\Tables\Column;

#[UseIn(Column\Int_::class)]
trait IntTrait
{
    use ColumnTrait;

    public function create(Blueprint $table, string $prefix): void
    {
        /* @var Column\Int_|static $this */
        $column = $table->integer("{$prefix}{$this->property->name}");

        if ($this->unsigned) {
            $column->unsigned();
        }

        if ($this->property->nullable) {
            $column->nullable();
        }

        if ($this->references) {
            $foreign = $table->foreign("{$prefix}{$this->property->name}")
                ->references($this->references_column)
                ->on($this->references_table);

            if ($this->on_delete) {
                $foreign->onDelete($this->on_delete);
            }
        }
    }
}