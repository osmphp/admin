<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Framework\TestCase;

class test_01_hello extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_app_name() {
        // GIVEN an app

        // WHEN you check its name
        $name = $this->app->name;

        // THEN it's the sample application's name
        $this->assertEquals('Osm_Data_Samples', $name);
    }
}