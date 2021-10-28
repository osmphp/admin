<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Accounts\Accounts;
use Osm\Framework\TestCase;

class test_02_users extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_simple_query() {
        // GIVEN sample classes and queries

        // WHEN you retrieve all products
        $count = Accounts::new()->get()->count;

        // THEN it's the sample application's name
        $this->assertEquals(1, $count);
    }
}