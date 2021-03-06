<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;

abstract class Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void {
        throw new NotImplemented($this);
    }
}