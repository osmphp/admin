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
        $this->loadSchema(Product::class);
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
        $this->loadSchema(Product::class, 2);
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
        $this->loadSchema(Product::class, 3);
        $this->app->schema->migrate();

        // THEN NULL values are converted to falsy values
        $this->assertEquals('-', $this->app->db->table('products')
            ->where('id', $id)
            ->value('description'));
    }

    public function disabled_test_clear() {
        // GIVEN database altered by previous tests

        // WHEN you clear the cache and DB for other tests
        $this->clear();

        // THEN `products` and other tables no longer exist
        $this->assertFalse($this->app->db->exists('products'));
    }
}