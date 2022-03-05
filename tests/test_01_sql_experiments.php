<?php

namespace Osm\Admin\Tests;

use Osm\Admin\Samples\Generics\Item;
use Osm\Framework\TestCase;
use function Osm\query;

class test_01_sql_experiments extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_select_int_array(): void {
        // GIVEN sample tables and data

        // WHEN you retrieve a record with int array
        $items = $this->app->db->connection->select(<<<EOT
SELECT 
		JSON_EXTRACT(`items`.`_data`, '$.int_array') AS `int_array` 
FROM `items`
EOT);

        // THEN it's actually a JSON string that requires decoding
        $this->assertEquals([1, 2, 3], json_decode($items[0]->int_array));
    }

    public function test_select_int(): void {
        // GIVEN sample tables and data

        // WHEN you retrieve a record with an implicit int
        $items = $this->app->db->connection->select(<<<EOT
SELECT
    `items`.`id`,
    JSON_EXTRACT(`items`.`_data`, '$.int') AS `int` 
FROM `items`
EOT);

        // THEN it's actually a JSON string containing int
        $this->assertEquals(5, json_decode($items[0]->int));
    }

    public function test_select_int_expr(): void {
        // GIVEN sample tables and data

        // WHEN you retrieve a record with an operation on an implicit int
        $items = $this->app->db->connection->select(<<<EOT
SELECT
    `items`.`id`,
    JSON_EXTRACT(`items`.`_data`, '$.int') - 4 AS `int` 
FROM `items`
EOT);

        // THEN it's actually an int
        $this->assertEquals(1, $items[0]->int);
    }

}