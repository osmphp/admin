<?php

namespace Osm\Admin\Storages;

use Osm\Admin\Base\Traits\SubTypes;
use Osm\Admin\Schema\Class_;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_ $class
 * @property ?string $query_class_name #[Serialized]
 */
class Storage extends Object_
{
    use SubTypes;

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }
}