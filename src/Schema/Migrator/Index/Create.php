<?php

namespace Osm\Admin\Schema\Migrator\Index;

use Osm\Admin\Schema\Migrator;
use Osm\Core\Exceptions\Required;

/**
 * @property string $index_name
 * @property ?bool $drop_if_exists
 */
class Create extends Migrator
{
    protected function get_index_name(): string {
        throw new Required(__METHOD__);
    }
}