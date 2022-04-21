<?php

namespace Osm\Admin\Schema\Migrator;

use Osm\Admin\Schema\Migrator;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Core\Exceptions\Required;

/**
 * @property Table $table
 * @property \stdClass|PropertyObject|null $old
 * @property PropertyObject $new
 */
class Property extends Migrator
{
    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_new(): PropertyObject {
        throw new Required(__METHOD__);
    }

}