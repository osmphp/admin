<?php

namespace Osm\Data\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

/**
 * @property Hints\Schema $reflection
 */
class Hydrator extends Object_
{
    public function hydrate(): Schema {
        throw new NotImplemented($this);
    }
}