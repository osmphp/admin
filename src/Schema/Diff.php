<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Db\Db;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property OutputInterface $output
 * @property bool $dry_run
 * @property Db $db
 */
class Diff extends Object_
{
    protected function get_output(): OutputInterface {
        throw new Required(__METHOD__);
    }

    protected function get_dry_run(): bool {
        throw new Required(__METHOD__);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }
}