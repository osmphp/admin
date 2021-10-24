<?php

namespace Osm\Data\Tools\Generators;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property Project $project
 * @property OutputInterface $output
 */
class Generator extends Object_
{
    public function run(): void {
        throw new NotImplemented($this);
    }

    protected function get_output(): OutputInterface {
        // if output stream is not provided by the caller, write output to a
        // memory buffer
        return new BufferedOutput();
    }

    protected function get_project(): Project {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $module */
        $module = $osm_app->modules[Module::class];

        return $module->project;
    }
}