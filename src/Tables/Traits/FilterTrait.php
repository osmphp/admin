<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

trait FilterTrait
{
    public function addToTableQuery(): void {
        throw new NotImplemented($this);
    }
}