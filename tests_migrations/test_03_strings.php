<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Samples\Migrations\String_\V001\Product;
use Osm\Admin\Schema\TestCase;
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

    public function test_add_column() {
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

        // FINALLY, clear the cache and DB for other tests
        $this->app->cache->clear();
        $this->app->migrations()->fresh();
        $this->app->migrations()->up();
    }
}