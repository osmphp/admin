<?php

namespace Osm\Admin\Migrations\Traits;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Tables\Column;

#[UseIn(Column::class)]
trait ColumnTrait
{
    public function create(Blueprint $table, string $prefix): void
    {
        throw new NotImplemented($this);
    }
    public function createKey(Blueprint $table): void
    {
    }


    public function createScoped(Blueprint $table, string $prefix): void
    {
        $this->create($table, $prefix);
    }

}