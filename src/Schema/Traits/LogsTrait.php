<?php

namespace Osm\Admin\Schema\Traits;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Framework\Logs\Logs;

/**
 * @property Logger $migrations
 */
#[UseIn(Logs::class)]
trait LogsTrait
{
    protected function get_migrations(): Logger {
        global $osm_app; /* @var App $osm_app */

        $logger = new Logger('migrations');
        if ($osm_app->settings->logs?->migrations ?? false) {
            $logger->pushHandler(new RotatingFileHandler(
                "{$osm_app->paths->temp}/logs/migrations.log"));
        }

        return $logger;
    }
}