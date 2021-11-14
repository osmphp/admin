<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Samples\Products\Product;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Schema;
use Osm\Admin\Scopes\Scope;
use Osm\Admin\Tables\Column;
use Osm\Admin\Tables\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\TestCase;
use function Osm\dehydrate;
use function Osm\hydrate;

class test_02_migrations extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_product_table_with_id_column() {
        // GIVEN a schema
        $schema = $this->wake(Schema::new([
            'classes' => [
                'My\Product' => Class_::new([
                    'name' => 'My\Product',
                    'properties' => [
                        'id' => Property\Regular::new([
                            'name' => 'id',
                            'nullable' => false,
                        ]),
                    ],
                    'storage' => Table::new([
                        'name' => 'products',
                        'columns' => [
                            'id' => Column\Increments::new([
                                'name' => 'id',
                            ]),
                        ],
                    ]),
                    'grids' => [],
                    'forms' => [],
                ]),
            ],
            'icons' => [],
        ]));

        // WHEN you migrate the schema
        $schema->migrate();

        // THEN the tables are created as specified
        $this->assertTrue($this->app->db->exists('products'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('products', 'id'));
    }

    public function test_product_table_with_id_and_sku_columns() {
        // GIVEN a schema
        $schema = $this->wake(Schema::new([
            'classes' => [
                'My\Product' => Class_::new([
                    'name' => 'My\Product',
                    'properties' => [
                        'id' => Property\Regular::new([
                            'name' => 'id',
                            'nullable' => false,
                        ]),
                        'sku' => Property\Regular::new([
                            'name' => 'sku',
                            'nullable' => false,
                        ]),
                    ],
                    'storage' => Table::new([
                        'name' => 'products',
                        'columns' => [
                            'id' => Column\Increments::new([
                                'name' => 'id',
                            ]),
                            'sku' => Column\String_::new([
                                'name' => 'sku',
                            ]),
                        ],
                    ]),
                    'grids' => [],
                    'forms' => [],
                ]),
            ],
            'icons' => [],
        ]));

        // WHEN you migrate the schema
        $schema->migrate();

        // THEN the tables are created as specified
        $this->assertTrue($this->app->db->exists('products'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('products', 'id'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('products', 'sku'));
    }

    public function test_no_tables() {
        // GIVEN a schema
        $schema = $this->wake(Schema::new([
            'classes' => [],
            'icons' => [],
        ]));

        // WHEN you migrate the schema
        $schema->migrate();

        // THEN the tables are created as specified
        $this->assertFalse($this->app->db->exists('products'));
    }

    protected function wake(Schema $schema): Schema
    {
        return hydrate(Schema::class, dehydrate($schema));
    }
}