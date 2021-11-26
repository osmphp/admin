<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Admin\Queries\Filter;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Filter::class)]
trait FilterTrait
{
    public function addToTableQuery(): void {
        throw new NotImplemented($this);
    }
}