<?php

namespace Osm\Admin\Schema\Class_;

use Osm\Admin\Schema\Class_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property string $base_class_name #[Serialized]
 * @property Class_ $base_class
 * @property string $type_name #[Serialized]
 */
class Type extends Class_
{
    protected function get_base_class_name(): string {
        throw new NotImplemented($this);
    }

    protected function get_base_class(): Class_ {
        throw new NotImplemented($this);
    }

    protected function get_type_name(): string {
        throw new NotImplemented($this);
    }

}