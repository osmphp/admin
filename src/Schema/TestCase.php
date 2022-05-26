<?php

namespace Osm\Admin\Schema;

use Osm\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function loadSchemaFixture(string $className,
        int $version = 1): Schema
    {
        $this->app->cache->clear();
        return $this->app->schema = $this->app->cache->get('schema', fn() =>
            Schema::new([
                'fixture_class_name' => $className,
                'fixture_version' => $version,
            ])->parse()
        );
    }

    protected function loadSchema(): Schema {
        $this->app->cache->clear();
        return $this->app->schema = $this->app->cache->get('schema', fn() =>
            Schema::new()->parse()
        );
    }

    protected function clear(): void {
        $this->app->cache->clear();
        $this->app->migrations()->fresh();
        $this->app->migrations()->up();
    }
}