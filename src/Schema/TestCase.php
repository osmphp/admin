<?php

namespace Osm\Admin\Schema;

use Osm\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function loadSchema(string $className, int $version = 1): void {
        $this->app->cache->clear();
        $this->app->schema = $this->app->cache->get('schema', fn() =>
            Schema::new([
                'fixture_class_name' => $className,
                'fixture_version' => $version,
            ])->parse()
        );
    }

    protected function clear(): void {
        $this->app->cache->clear();
        $this->app->migrations()->fresh();
        $this->app->migrations()->up();
    }
}