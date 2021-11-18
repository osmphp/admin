<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Samples\Products\Product;
use Osm\Framework\TestCase;

class test_14_classes extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_regular_properties() {
        // GIVEN sample classes

        // WHEN you check the definition of the `Product` class
        $class = $this->app->schema->classes[Product::class];

        // THEN it has property definitions
        $this->assertArrayHasKey('sku', $class->properties);
    }
}