<?php

namespace Osm\Admin\Schema\Commands;

use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Framework\Console\Command;
use function Osm\dehydrate;

/**
 * @property Schema $schema
 */
class Export extends Command
{
    public string $name = 'export:schema';

    public function run(): void {
        $this->output->writeln(json_encode(dehydrate($this->schema),
            JSON_PRETTY_PRINT));
    }

    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema;
    }
}