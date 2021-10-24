<?php

namespace Osm\Data\Schema;

use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\BaseModule;
use Osm\Core\Class_;
use Osm\Core\Exceptions\AttributeRequired;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Object_;
use Osm\Core\Property;
use Osm\Data\Tables\Table as TableDef;
use Osm\Data\Schema\Hints;
use function Osm\__;
use function Osm\get_descendant_classes_by_name;
use Osm\Data\Tables\Attributes;

/**
 * Extracts schema information in plain object format.
 *
 * For the means of code generation, execute it in context
 * of the `Osm_Project` application, and let it be garbage-collected afterwards.
 *
 * For the means of runtime table schema reflection, execute it in the
 * current application context.
 *
 * @property BaseModule[] $project_modules
 * @property string $project_package_name
 */
class Reflector extends Object_
{
    public function reflect(): \stdClass|Hints\Schema {
        return (object)[
            'tables' => $this->reflectTables(),
        ];
    }

    /**
     * @return Hints\Table[]
     */
    protected function reflectTables(): array {
        global $osm_app; /* @var App $osm_app */

        $tables = [];

        $tableClassNames = get_descendant_classes_by_name(
            TableDef::class);
            
        foreach ($tableClassNames as $name => $tableClassName) {
            $recordClassName = $this->getRecordClassName($tableClassName);

            $tables[$name] = $this->reflectRecordClass($name,
                $osm_app->classes[$recordClassName]);
        }

        return $tables;
    }

    protected function reflectRecordClass(string $tableName,
        Class_ $recordClass): \stdClass|Hints\Table
    {
        global $osm_app; /* @var App $osm_app */

        $table = (object)[
            'name' => $tableName,
            'module_class_name' => $recordClass->module_class_name,
            'properties' => [],
        ];

        foreach ($recordClass->properties as $property) {
            $this->reflectRecordProperty($table, $recordClass,
                $property);
        }

        $typeClassNames = get_descendant_classes_by_name($recordClass->name);

        foreach ($typeClassNames as $name => $typeClassName) {
            $this->reflectRecordTypeClass($table, $recordClass, $name,
                $osm_app->classes[$typeClassName]);
        }
    }

    protected function reflectRecordTypeClass(\stdClass|Hints\Table $table,
        Class_ $recordClass, string $typeName, Class_ $typeClass): void
    {
        foreach ($typeClass->properties as $property) {
            $this->reflectRecordTypeProperty($table, $typeName,
                $typeClass, $property);
        }
    }

    protected function reflectRecordProperty(\stdClass|Hints\Table $table,
        Class_ $recordClass, Property $property): void
    {
        if (!isset($property->attributes[Serialized::class])) {
            return;
        }

        $moduleClassName = is_subclass_of($recordClass->name,
            $property->class_name, true)
                ? $recordClass->module_class_name
                : $property->module_class_name;

        if (!$moduleClassName) {
            return;
        }

        $this->addProperty($table, $moduleClassName, $recordClass, $property);
    }

    protected function addProperty(\stdClass|Hints\Class_ $class,
        string $moduleClassName, Class_ $recordClass, Property $property): void
    {
        throw new NotImplemented($this);
    }

    protected function reflectRecordTypeProperty(\stdClass|Hints\Table $table,
        string $typeName, Class_ $typeClass, Property $property): void
    {
        if (!isset($property->attributes[Serialized::class])) {
            return;
        }

        throw new NotImplemented($this);
    }

    protected function get_project_modules(): array {
        global $osm_app; /* @var App $osm_app */

        return array_filter($osm_app->modules,
            fn(BaseModule $module) =>
                $module->package_name === $this->project_package_name);
    }

    protected function get_project_package_name(): string {
        global $osm_app; /* @var App $osm_app */

        foreach ($osm_app->packages as $package) {
            if (!$package->path) {
                return $package->name;
            }
        }

        throw new NotSupported(__("One of the application packages should be the project package, the project's `composer.json`"));
    }

    protected function getRecordClassName(string $tableClassName): string
    {
        global $osm_app; /* @var App $osm_app */

        $class = $osm_app->classes[$tableClassName];

        /* @var Attributes\Record $attribute */
        if (!($attribute = $class->attributes[Attributes\Record::class]
            ?? null))
        {
            throw new AttributeRequired(Attributes\Record::class,
                $class->name);
        }

        return $attribute->class_name;
    }
}