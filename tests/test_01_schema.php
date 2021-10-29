<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Samples\Products\Product;
use Osm\Data\Samples\Products\ProductTable;
use Osm\Framework\TestCase;

class test_01_schema extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_root_class() {
        // GIVEN sample classes and queries

        // WHEN you check the definition of the `Product` class
        $class = $this->app->schema->classes[Product::class];

        // THEN it's the sample application's name
        $this->assertArrayHasKey('sku', $class->properties);
    }
}