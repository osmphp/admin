<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Framework\TestCase;

class test_11_accounts extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_migrate_up() {
        // GIVEN sample classes and queries

        // WHEN you run schema migrations
        $this->app->schema->migrate();

        // THEN it's the sample application's name
        $this->assertTrue($this->app->db->exists('accounts'));
    }
}