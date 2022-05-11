<?php

namespace Osm\Admin\Schema\Traits;

use Monolog\Logger;
use Osm\Core\App;

/**
 * @property Logger $log
 */
trait LogsMigrations
{
    protected function get_log(): Logger {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->logs->migrations;
    }

    protected function logTable(string $message): void {
        $this->log->notice($message);
    }

    protected function logProperty(string $message): void {
        $this->log->notice('    ' . $message);
    }

    protected function logAttribute(string $message): void {
        $this->log->notice('        ' . $message);
    }
}