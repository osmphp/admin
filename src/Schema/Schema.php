<?php

namespace Osm\Data\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_[] $classes #[Serialized]
 */
class Schema extends Object_
{
    /**
     * @var int
     */
    #[Serialized]
    public int $version = 1;

    protected function get_classes(): array {
        return Reflector::new()->getClasses();
    }
}