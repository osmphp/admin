<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Samples\Products\Product;
use Osm\Admin\Scopes\Scope;
use Osm\Admin\Tables\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\TestCase;

class test_03_scopes extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_schema() {
        // GIVEN `Scope` class definition

        // WHEN you check how it's represented in the schema
        $class = $this->app->schema->classes[Scope::class];

        // THEN the information about properties matches the
        // class definition
        $this->assertEquals(Scope::class, $class->name);

        $this->assertArrayHasKey('id', $class->properties);
        $property = $class->properties['id'];
        $this->assertEquals('regular', $property->type);
        $this->assertEquals('int', $property->reflection->type);

        $this->assertArrayHasKey('parent_id', $class->properties);
        $property = $class->properties['parent_id'];
        $this->assertEquals('regular', $property->type);
        $this->assertEquals('int', $property->reflection->type);

        $this->assertArrayHasKey('title', $class->properties);
        $property = $class->properties['title'];
        $this->assertEquals('regular', $property->type);
        $this->assertEquals('string', $property->reflection->type);

        // AND the information about table columns matches the class definition
        $this->assertNotNull($class->storage);
        $this->assertInstanceOf(Table::class, $class->storage);
        /* @var Table $table */
        $table = $class->storage;

        $this->assertArrayHasKey('id', $table->columns);
        $this->assertArrayHasKey('parent_id', $table->columns);
        $this->assertArrayNotHasKey('title', $table->columns);

        //throw new NotImplemented($this);
    }

    public function test_migrations() {
        // GIVEN `Scope` class definition

        // WHEN you migrate the whole schema
        $this->app->schema->migrate();

        // THEN `scopes` table is created with the columns specified in the PHP
        // attributes
        $this->assertTrue($this->app->db->exists('scopes'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('scopes', 'id'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('scopes', 'parent_id'));
        $this->assertTrue($this->app->db->connection->getSchemaBuilder()
            ->hasColumn('scopes', 'data'));

        // AND the root scope is created and its indexed
        // properties are computed correctly
        $rootScope = $this->app->db->table('scopes')
            ->whereNull('parent_id')
            ->first();
        $this->assertNotNull($rootScope);
        $this->assertEquals(0, $rootScope->level);
        $this->assertTrue($rootScope->id_path === (string)$rootScope->id);

        // AND root scope-specific tables are created, too
        $this->assertTrue($this->app->db->exists("s{$rootScope->id}__products"));
    }
}