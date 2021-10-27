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
    protected function get_classes(): array {
        return Reflector::new()->getClasses();
    }
}