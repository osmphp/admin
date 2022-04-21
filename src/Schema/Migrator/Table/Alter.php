<?php

namespace Osm\Admin\Schema\Migrator\Table;

use Osm\Admin\Schema\Migrator;
use Osm\Core\Exceptions\Required;

/**
 * @property string $table_name
 */
class Alter extends Migrator
{
    protected function get_table_name(): string {
        throw new Required(__METHOD__);
    }
}