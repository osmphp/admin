<?php

declare(strict_types=1);

namespace Osm\Admin\Tests;

use Osm\Admin\Samples\Products\Products;
use Osm\Framework\TestCase;

class test_12_products extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_simple_query() {
        // GIVEN sample classes and queries

        // WHEN you retrieve all products
        $result = Products::new()->get();

        // THEN it's the sample application's name
        $this->assertCount(0, $result->items);
    }
}