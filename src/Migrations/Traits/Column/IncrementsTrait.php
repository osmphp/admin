<?php

namespace Osm\Data\Migrations\Traits\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\UseIn;
use Osm\Data\Migrations\Traits\ColumnTrait;
use Osm\Data\Tables\Column;

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

    public function createScope(Blueprint $table, string $prefix): void
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