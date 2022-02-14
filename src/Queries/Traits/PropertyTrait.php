<?php

namespace Osm\Admin\Queries\Traits;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Property::class)]
trait PropertyTrait
{
    public function resolve(Formula\Identifier $identifier): void {
        throw new NotImplemented($this);
    }
}