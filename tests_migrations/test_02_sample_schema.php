<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Samples\Generics\Item;
use Osm\Admin\Samples\Generics\Related;
use Osm\Framework\TestCase;

class test_02_sample_schema extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_item() {
        // GIVEN the `Item` class schema reflected from PHP class definition
        $struct = $this->app->schema->tables[Item::class];

        // WHEN you check it

        // THEN it "knows" it's table name and all its sub types
        $this->assertEquals('table', $struct->type);
        $this->assertEquals('items', $struct->table_name);
        $this->assertEquals([
            'type1' => Item\Type1::class,
            'type2' => Item\Type2::class,
        ], $struct->type_class_names);

        // AND it "knows" how a regular property is defined
        $property = $struct->properties['id'];
        $this->assertEquals('id', $property->name);
        $this->assertEquals([], $property->if);
        $this->assertEquals('int', $property->type);
        $this->assertEquals(false, $property->nullable);
        $this->assertEquals(true, $property->explicit);
        $this->assertEquals(true, $property->unsigned);
        $this->assertEquals(true, $property->auto_increment);

        // AND it "knows" how a type-specific property is defined
        $property = $struct->properties['type1_string'];
        $this->assertEquals('type1_string', $property->name);
        $this->assertEquals(['type' => ['type1']], $property->if);
        $this->assertEquals('string', $property->type);
        $this->assertEquals(false, $property->nullable);

        // AND it "knows" how a reference to another record is defined
        $property = $struct->properties['record'];
        $this->assertEquals('record', $property->name);
        $this->assertEquals('record', $property->type);
        $this->assertEquals(false, $property->nullable);
        $this->assertEquals(true, $property->explicit);
        $this->assertEquals(Related::class, $property->ref_class_name);
        $this->assertEquals(null, $property->ref_if);
    }
}