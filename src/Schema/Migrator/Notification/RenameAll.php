<?php

namespace Osm\Admin\Schema\Migrator\Notification;

use Osm\Admin\Schema\Migrator;
use Osm\Core\Exceptions\Required;

/**
 * @property string $old_table_name
 * @property string $table_name
 */
class RenameAll extends Migrator
{
    protected function get_old_table_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_table_name(): string {
        throw new Required(__METHOD__);
    }
}