<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Samples\Migrations\String_\V001\Product;
use Osm\Admin\Schema\TestCase;
use function Osm\query;
use function Osm\ui_query;

class test_03_strings extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_create_table() {
        // GIVEN empty database
        $this->assertFalse($this->app->db->exists('products'));

        // WHEN you run `V1` migration
        $this->loadSchemaFixture(Product::class);
        $this->app->schema->migrate();

        // THEN initial product table is created
        $this->assertTrue($this->app->db->exists('products'));
    }

    public function test_add_explicit_property() {
        // GIVEN database with `V1` schema and some data
        $id = ui_query(Product::class)->insert((object)[
            'title' => 'Lorem ipsum',
        ]);

        // WHEN you run `V2` migration
        $this->loadSchemaFixture(Product::class, 2);
        $this->app->schema->migrate();

        // THEN new nullable column is added
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('products', 'description'));

        // AND `NULL` value for it is added to existing data
        $this->assertNull($this->app->db->table('products')
            ->where('id', $id)
            ->value('description'));
    }

    public function test_make_explicit_property_non_nullable() {
        // GIVEN database with `V2` schema and some data
        $id = query(Product::class)
            ->where("title = 'Lorem ipsum'")
            ->value("id");

        // WHEN you run `V3` migration
        $this->loadSchemaFixture(Product::class, 3);
        $this->app->schema->migrate();

        // THEN NULL values are converted to falsy values
        $this->assertEquals('-', $this->app->db->table('products')
            ->where('id', $id)
            ->value('description'));
    }

    public function test_conversion_from_int() {
        // GIVEN database with `V4` schema and some data
        $this->loadSchemaFixture(Product::class, 4);
        $this->app->schema->migrate();

        $id = query(Product::class)
            ->where("title = 'Lorem ipsum'")
            ->value("id");

        query(Product::class)
            ->where("id = {$id}")
            ->update(['color' => 0xFFFFFF]);

        // WHEN you run `V5` migration
        $this->loadSchemaFixture(Product::class, 5);
        $this->app->schema->migrate();

        // THEN `color` is converted from int
        $this->assertSame((string)0xFFFFFF,
            $this->app->db->table('products')
                ->where('id', $id)
                ->value('color'));
    }

    public function test_conversion_to_int() {
        // GIVEN database with `V5` schema and some data
        $id1 = query(Product::class)
            ->where("title = 'Lorem ipsum'")
            ->value("id");
        $id2 = ui_query(Product::class)->insert((object)[
            'title' => 'Invalid color',
            'description' => 'Invalid color',
            'color' => 'black', // non-numeric
        ]);

        // WHEN you run `V6` migration
        $this->loadSchemaFixture(Product::class, 6);
        $this->app->schema->migrate();

        // THEN `color` is converted to int
        $this->assertSame(0xFFFFFF, $this->app->db->table('products')
            ->where('id', $id1)
            ->value('color'));
        $this->assertSame(0, $this->app->db->table('products')
            ->where('id', $id2)
            ->value('color'));
    }

    public function disabled_test_clear() {
        // GIVEN database altered by previous tests

        // WHEN you clear the cache and DB for other tests
        $this->clear();

        // THEN `products` and other tables no longer exist
        $this->assertFalse($this->app->db->exists('products'));
    }
}