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
use Osm\Core\Object_;
use Osm\Framework\TestCase;
use function Osm\dehydrate;
use function Osm\hydrate;

class test_02_migrations extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_product_table_with_id_column() {
        // GIVEN a schema
        /* @var Schema $schema */
        $schema = hydrate(Schema::class, json_decode(<<<EOT
{
    "classes": {
        "Product": {
            "name": "Product",
            "properties": {
                "id": {
                    "name": "id",
                    "type": "regular",
                    "nullable": false
                }
            },
            "storage": {
                "name": "products",
                "type": "table",
                "columns": {
                    "id": {
                        "name": "id",
                        "type": "increments"
                    }
                }
            },
            "grids": [],
            "forms": []
        }
    },
    "icons": []
}
EOT));

        // WHEN you migrate the schema
        $schema->migrate();

        // THEN the tables are created as specified
        $this->assertTrue($this->app->db->exists('products'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('products', 'id'));
    }

    public function test_product_table_with_id_and_sku_columns() {
        // GIVEN a schema
        /* @var Schema $schema */
        $schema = hydrate(Schema::class, json_decode(<<<EOT
{
    "classes": {
        "Product": {
            "name": "Product",
            "properties": {
                "id": {
                    "name": "id",
                    "type": "regular",
                    "nullable": false
                },
                "sku": {
                    "name": "sku",
                    "type": "regular",
                    "nullable": false
                }
            },
            "storage": {
                "name": "products",
                "type": "table",
                "columns": {
                    "id": {
                        "name": "id",
                        "type": "increments"
                    },
                    "sku": {
                        "name": "sku",
                        "type": "string"
                    }
                }
            },
            "grids": [],
            "forms": []
        }
    },
    "icons": []
}
EOT));

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
        /* @var Schema $schema */
        $schema = hydrate(Schema::class, json_decode(<<<EOT
{
    "classes": [],
    "icons": []
}
EOT));

        // WHEN you migrate the schema
        $schema->migrate();

        // THEN the tables are created as specified
        $this->assertFalse($this->app->db->exists('products'));
    }
}