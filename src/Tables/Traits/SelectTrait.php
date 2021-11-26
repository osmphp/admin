<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Admin\Queries\Select;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Select::class)]
trait SelectTrait
{
    public function addToTableQuery(): void {
        throw new NotImplemented($this);
    }
}