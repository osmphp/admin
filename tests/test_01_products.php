<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Samples\Products\Products;
use Osm\Framework\TestCase;

class test_01_products extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_simple_query() {
        // GIVEN sample classes and queries

        // WHEN you retrieve all products
        $count = Products::new()->get()->count;

        // THEN it's the sample application's name
        $this->assertEquals(1, $count);
    }
}