<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Framework\TestCase;

class test_01_products extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_simple_query() {
        // GIVEN an app

        // WHEN you check its name
        $name = $this->app->name;

        // THEN it's the sample application's name
        $this->assertEquals('Osm_Data_Samples', $name);
    }
}