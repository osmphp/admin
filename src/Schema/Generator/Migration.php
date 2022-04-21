<?php

namespace Osm\Admin\Schema\Generator;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

class Migration extends Object_
{
    public function generate(): void {
        throw new NotImplemented($this);
    }
}