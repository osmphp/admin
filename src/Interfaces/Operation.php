<?php

namespace Osm\Admin\Interfaces;

use Osm\Admin\Schema\Class_;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Class_ $class
 * @property string $name
 * @property string $title
 */
class Operation extends Object_
{
    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }
}