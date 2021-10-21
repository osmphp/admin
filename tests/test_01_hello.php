<?php

declare(strict_types=1);

namespace My\Tests;

use Osm\Framework\TestCase;

class test_01_hello extends TestCase
{
    public string $app_class_name = \My\Samples\App::class;

    public function test_app_name() {
        // GIVEN an app

        // WHEN you check its name
        $name = $this->app->name;

        // THEN it's the sample application's name
        $this->assertEquals('My_Samples', $name);
    }
}