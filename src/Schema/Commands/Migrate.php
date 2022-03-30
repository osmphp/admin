<?php

namespace Osm\Admin\Schema\Commands;

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
        global $osm_app; /* @var App $osm_app */
        $osm_app->migrations()->fresh();
        $osm_app->migrations()->up();

        // `$osm_app->schema` is stored in cache. It contains information
        // fetched from the database, for example, indexer IDs. Hence, after
        // resetting the database it's necessary to "forget" the currently
        // cached schema and reflect it from code anew
        $osm_app->cache->deleteItem('schema');
        unset($osm_app->schema);

        $osm_app->schema->migrate();
    }

    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema;
    }
}