<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Forms\Form;
use Osm\Admin\Grids\Column;
use Osm\Admin\Grids\Grid;
use Osm\Admin\Icons\Icon;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Schema;
use Osm\Admin\Scopes\Scope;
use Osm\Admin\Scopes\ScopedTable;
use Osm\Admin\Storages\Storage;
use Osm\Admin\Tables\Column as TableColumn;
use Osm\Admin\Tables\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Framework\Areas\Admin;
use Osm\Framework\TestCase;
use Osm\Admin\Forms\Field;
use function Osm\dehydrate;
use function Osm\hydrate;

class test_01_schema_hydration extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_hydration() {
        // GIVEN a schema reflected from PHP classes
        $schema = $this->app->schema;

        // WHEN you dehydrate it and then hydrate it back again
        $dehydrated = dehydrate($schema);
        $hydrated = hydrate(Schema::class, $dehydrated);

        // THEN it's the exact copy of the original schema
        $this->assertSchemaHydrated($schema, $hydrated);
    }

    public function test_serialization() {
        // GIVEN a schema reflected from PHP classes
        $schema = $this->app->schema;

        // WHEN you serialize it and then unserialize it back again
        $serialized = serialize($schema);
        $unserialized = unserialize($serialized);

        // THEN it's the exact copy of the original schema
        $this->assertSchemaHydrated($schema, $unserialized);
    }

    protected function assertPropertiesEqual(Object_ $original,
        Object_ $hydrated, array $propertyNames): void
    {
        foreach ($propertyNames as $propertyName) {
            $this->assertTrue($original->$propertyName ===
                $hydrated->$propertyName);
        }
    }

    protected function assertArrayKeysEqual(Object_ $original,
        Object_ $hydrated, array $propertyNames): void
    {
        foreach ($propertyNames as $propertyName) {
            $this->assertTrue(is_array($original->$propertyName),
                $original::class . '::$' . "{$propertyName} must be an array");
            $this->assertTrue(array_keys($original->$propertyName) ==
                array_keys($hydrated->$propertyName));
        }
    }

    protected function assertSchemaHydrated(Schema $original,
        Schema $hydrated): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertArrayKeysEqual($original, $hydrated, ['classes']);

        foreach ($original->classes as $key => $class) {
            $this->assertClassHydrated($class, $hydrated->classes[$key],
                $hydrated);
        }
    }

    protected function assertClassHydrated(Class_ $original,
        Class_ $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->schema === $hydratedSchema);
        $this->assertPropertiesEqual($original, $hydrated,
            ['name', 'reflection', 'type']);
        $this->assertArrayKeysEqual($original, $hydrated,
            ['properties', 'type_class_names']);

        foreach ($original->properties as $key => $property) {
            $this->assertPropertyHydrated($property, $hydrated->properties[$key],
                $hydratedSchema);
        }
    }

    protected function assertPropertyHydrated(Property $original,
        Property $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->class ===
            $hydratedSchema->classes[$hydrated->class->name]);
        $this->assertPropertiesEqual($original, $hydrated,
            ['name', 'type', 'reflection', 'nullable', 'array', 'explicit',
                'virtual', 'formula', 'overridable', 'control_class_name',
                'before', 'after', 'in', 'default', 'unsigned', 'size',
                'precision', 'scale', 'max_length', 'on_delete']);
    }
}