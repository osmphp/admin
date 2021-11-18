<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Framework\TestCase;

class test_03_indexing extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_insert() {
        // GIVEN a migrated schema

        // WHEN you insert a record

        // THEN its indexed properties are properly computed
    }
}