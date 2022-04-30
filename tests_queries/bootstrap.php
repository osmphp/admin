<?php

declare(strict_types=1);

use Osm\Admin\Samples\App;
use Osm\Admin\Schema\Schema;
use Osm\Runtime\Apps;

require 'vendor/autoload.php';
umask(0);

try {
    Apps::$project_path = dirname(__DIR__);
    Apps::compile(App::class);
    Apps::run(Apps::create(App::class), function(App $app) {
        $app->cache->clear();
        $app->migrations()->fresh();
        $app->migrations()->up();

        $app->schema = $app->cache->get('schema', fn() =>
            Schema::new([
                'fixture_class_name' =>
                    \Osm\Admin\Samples\Queries\V001\Product::class,
                'fixture_version' => 1,
            ])->parse()
        );

        $app->schema->migrate();
    });
}
catch (Throwable $e) {
    echo "{$e->getMessage()}\n{$e->getTraceAsString()}\n";
    throw $e;
}
