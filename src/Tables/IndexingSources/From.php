<?php

namespace Osm\Admin\Tables\IndexingSources;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Indexing\Source;
use Osm\Core\Attributes\Type;

#[Type('from')]
class From extends Source
{
    public function createNotificationTable(Blueprint $table): void {
        $table->unsignedInteger('id')->primary();

        $table->foreign('id')
            ->references('id')->on($this->table)
            ->onDelete('cascade');
    }
}