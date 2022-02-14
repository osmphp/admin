<?php

namespace Osm\Admin\Tests;

use Osm\Admin\Samples\Generics\Item;
use Osm\Framework\TestCase;
use function Osm\query;

class test_02_sql_generation extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_select_id(): void {
        // GIVEN tables and classes defined in the sample application

        // WHEN you retrieve the ID of the first created object
        $item = query(Item::class)
            ->orderBy('id')
            ->first('id');

        // THEN it's 1
        $this->assertSame(1, $item->id);
    }

    public function test_select_int(): void {
        // GIVEN tables and classes defined in the sample application

        // WHEN you retrieve the `int` property the first created object
        $item = query(Item::class)
            ->orderBy('id')
            ->first('int');

        // THEN it's 1
        $this->assertSame(5, $item->int);
    }

    public function test_where_equals(): void {
        // GIVEN tables and classes defined in the sample application

        // WHEN you retrieve the `int` property the first created object
        $item = query(Item::class)
            ->where('id = 1')
            ->first('int');

        // THEN it's 1
        $this->assertSame(5, $item->int);
    }
}