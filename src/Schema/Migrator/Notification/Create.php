<?php

namespace Osm\Admin\Schema\Migrator\Notification;

use Osm\Admin\Schema\Migrator;
use Osm\Core\Exceptions\Required;

/**
 * @property string $table_name
 */
class Create extends Migrator
{
    protected function get_table_name(): string {
        throw new Required(__METHOD__);
    }
}