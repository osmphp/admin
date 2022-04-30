<?php

namespace Osm\Admin\TestsQueries;

use Osm\Admin\Samples\Generics\Item;
use Osm\Admin\Samples\Queries\V001\Product;
use Osm\Framework\TestCase;
use function Osm\query;

class test_01_sql_generation extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;
    public bool $use_db = true;

    public function test_zero_count(): void {
        // GIVEN a schema defined in the `Osm\Admin\Samples\Queries\V001`
        // namespace

        // WHEN you count records in an empty table
        $count = query(Product::class)
            ->value("COUNT() AS count");

        // THEN it's 0
        $this->assertEquals(0, $count);
    }

    public function test_bulk_insert(): void {
        // GIVEN a schema defined in the `Osm\Admin\Samples\Queries\V001`
        // namespace, and some data
        $id = query(Product::class)->insert([
            'title' => 'Lorem ipsum',
        ]);

        // WHEN you bulk update descriptions
        query(Product::class)
            ->select("description ?? '-' AS description")
            ->bulkUpdate();
        $description = query(Product::class)
            ->where("id = {$id}")
            ->value("description");

        // THEN they indeed change
        $this->assertEquals('-', $description);
    }
}