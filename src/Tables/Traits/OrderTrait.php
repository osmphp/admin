<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Admin\Queries\Order;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Order::class)]
trait OrderTrait
{
    public function addToTableQuery(): void {
        throw new NotImplemented($this);
    }
}