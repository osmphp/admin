<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property OutputInterface $output
 * @property bool $dry_run
 */
class Migrator extends Object_
{
    protected function get_output(): OutputInterface {
        throw new Required(__METHOD__);
    }

    protected function get_dry_run(): bool {
        throw new Required(__METHOD__);
    }

    public function migrate(): void {
        throw new NotImplemented($this);
    }
}