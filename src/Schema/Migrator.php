<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

class Migrator extends Object_
{
    public function migrate(): void {
        throw new NotImplemented($this);
    }
}