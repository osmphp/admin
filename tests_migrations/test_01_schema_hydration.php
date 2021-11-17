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

        $this->assertPropertiesEqual($original, $hydrated, ['version']);
        $this->assertArrayKeysEqual($original, $hydrated, ['classes', 'icons']);

        foreach ($original->classes as $key => $class) {
            $this->assertClassHydrated($class, $hydrated->classes[$key],
                $hydrated);
        }

        foreach ($original->icons as $key => $icon) {
            $this->assertIconHydrated($icon, $hydrated->icons[$key],
                $hydrated);
        }
    }

    protected function assertClassHydrated(Class_ $original,
        Class_ $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->schema === $hydratedSchema);
        $this->assertPropertiesEqual($original, $hydrated,
            ['name', 'reflection']);
        $this->assertArrayKeysEqual($original, $hydrated,
            ['properties', 'types', 'grids', 'forms']);

        foreach ($original->properties as $key => $property) {
            $this->assertPropertyHydrated($property, $hydrated->properties[$key],
                $hydratedSchema);
        }

        foreach ($original->grids as $key => $grid) {
            $this->assertGridHydrated($grid, $hydrated->grids[$key],
                $hydratedSchema);
        }
        foreach ($original->forms as $key => $form) {
            $this->assertFormHydrated($form, $hydrated->forms[$key],
                $hydratedSchema);
        }

        $this->assertStorageHydrated($original->storage, $hydrated->storage,
            $hydratedSchema);
    }

    protected function assertIconHydrated(Icon $original,
        Icon $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->schema === $hydratedSchema);
        //throw new NotImplemented($this);
    }

    protected function assertPropertyHydrated(Property $original,
        Property $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->class ===
            $hydratedSchema->classes[$hydrated->class->name]);
        $this->assertPropertiesEqual($original, $hydrated,
            ['name', 'type', 'reflection', 'nullable', 'module_class_name']);
    }

    protected function assertGridHydrated(Grid $original,
        Grid $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->class ===
            $hydratedSchema->classes[$hydrated->class->name]);
        $this->assertPropertiesEqual($original, $hydrated,
            [
                'url', 'title', 'area_class_name', 'select', 'parameters',
                'multiselect', 'editable', 'can_create', 'name', 'type',
                'routes',
            ]);
    }

    protected function assertFormHydrated(Form $original,
        Form $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->class ===
            $hydratedSchema->classes[$hydrated->class->name]);
        //throw new NotImplemented($this);
    }

    protected function assertStorageHydrated(?Storage $original,
        ?Storage $hydrated, Schema $hydratedSchema): void
    {
        if (!$original && !$hydrated) {
            return;
        }

        $this->assertNotNull($original);
        $this->assertNotNull($hydrated);
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->class ===
            $hydratedSchema->classes[$hydrated->class->name]);

        if ($hydrated instanceof Table || $hydrated instanceof ScopedTable) {
            $this->assertTableHydrated($original, $hydrated, $hydratedSchema);
        }
        else {
            throw new NotImplemented($this);
        }
    }

    protected function assertTableHydrated(Table|Storage $original,
        Table|Storage $hydrated, Schema $hydratedSchema): void
    {
        $this->assertPropertiesEqual($original, $hydrated, ['name']);
        $this->assertArrayKeysEqual($original, $hydrated, ['columns']);

        foreach ($original->columns as $key => $column) {
            $this->assertTableColumnHydrated($column, $hydrated->columns[$key],
                $hydratedSchema);
        }
    }

    protected function assertTableColumnHydrated(TableColumn $original,
        TableColumn $hydrated, Schema $hydratedSchema): void
    {
        $this->assertTrue($original::class === $hydrated::class);

        $this->assertTrue($hydrated->table === $hydratedSchema
            ->classes[$hydrated->table->class->name]
            ->storage);
        $this->assertTrue($hydrated->property === $hydratedSchema
            ->classes[$hydrated->table->class->name]
            ->properties[$hydrated->name]);
        $this->assertPropertiesEqual($original, $hydrated,
            ['name', 'unsigned', 'references', 'on_delete']);
    }
}