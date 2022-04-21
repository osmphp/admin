<?php

namespace Osm\Admin\Schema\Migrator\Index;

use Osm\Admin\Schema\Migrator;
use Osm\Core\Exceptions\Required;

/**
 * @property string $old_index_name
 * @property string $index_name
 */
class Rename extends Migrator
{
    protected function get_old_index_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_index_name(): string {
        throw new Required(__METHOD__);
    }
}