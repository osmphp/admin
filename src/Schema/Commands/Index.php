<?php

namespace Osm\Admin\Schema\Commands;

use Osm\Admin\Schema\Indexer;
use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Framework\Console\Command;
use Osm\Framework\Console\Attributes\Option;
/**
 * @property Schema $schema
 * @property bool $full #[Option('f')]
 *
 * @uses Option
 */
class Index extends Command
{
    public string $name = 'index';

    public function run(): void
    {
        $this->schema->index($this->full
            ? Indexer::FULL
            : Indexer::PARTIAL);
    }

    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema;
    }
}