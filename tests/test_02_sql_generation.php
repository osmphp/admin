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

        // WHEN you retrieve the `int` property of the matching object
        $item = query(Item::class)
            ->where('id = 1')
            ->first('int');

        // THEN it's 1
        $this->assertSame(5, $item->int);
    }

    public function test_where_in(): void {
        // GIVEN tables and classes defined in the sample application

        // WHEN you retrieve the IDs on the matching objects
        $items = query(Item::class)
            ->where('id IN (1, 2)')
            ->get('id');

        // THEN it's 1 - the ID 2 is not there in the sample
        $this->assertCount(1, $items);
    }

    public function test_count(): void {
        // GIVEN tables and classes defined in the sample application

        // WHEN you retrieve the object count
        $count = query(Item::class)
            ->value('COUNT() AS count');

        // THEN it's 1 - there is only 1 object
        $this->assertSame(1, $count);
    }
}