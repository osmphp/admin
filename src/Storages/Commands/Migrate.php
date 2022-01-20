<?php

namespace Osm\Admin\Storages\Commands;

use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Framework\Console\Command;

/**
 * @property Schema $schema
 */
class Migrate extends Command
{
    public string $name = 'migrate:schema';

    public function run(): void
    {
        $this->schema->migrate();
    }

    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema;
    }
}