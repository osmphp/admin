<?php

namespace Osm\Admin\Tests;

use Osm\Admin\Samples\Generics\Item;
use Osm\Framework\TestCase;
use function Osm\query;

class test_01_dummy extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_dummy(): void {
        $this->assertTrue(true);
    }
}