<?php

namespace Osm\Admin\Migrations\Traits\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\UseIn;
use Osm\Admin\Migrations\Traits\ColumnTrait;
use Osm\Admin\Tables\Column;

#[UseIn(Column\Increments::class)]
trait IncrementsTrait
{
    use ColumnTrait;

    public function create(Blueprint $table, string $prefix): void
    {
        /* @var Column\Increments|static $this */
        $table->increments("{$prefix}{$this->property->name}");
    }

    public function createKey(Blueprint $table): void
    {
        /* @var Column\Increments|static $this */
        $table->increments($this->property->name);
    }

    public function createScoped(Blueprint $table, string $prefix): void
    {
        /* @var Column\Increments|static $this */
        $table->unsignedInteger("{$prefix}{$this->property->name}")
            ->primary();
        $table->foreign("{$prefix}{$this->property->name}")
            ->references($this->property->name)
            ->on($this->property->class->table)
            ->onDelete('cascade');
    }
}