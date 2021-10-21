<?php

declare(strict_types=1);

use My\Samples\App;
use Osm\Runtime\Apps;

require 'vendor/autoload.php';
umask(0);

try {
    Apps::$project_path = dirname(__DIR__);
    Apps::compile(App::class);
    Apps::run(Apps::create(App::class), function(App $app) {
        $app->cache->clear();
    });
}
catch (Throwable $e) {
    echo "{$e->getMessage()}\n{$e->getTraceAsString()}\n";
    throw $e;
}
