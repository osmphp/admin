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
use function Osm\get_descendant_classes;
use function Osm\get_descendant_classes_by_name;
use Osm\Data\Tables\Attributes;

/**
 * Extracts schema information in plain object format.
 *
 * Execute an instance of this class in context of `Osm_Project` application,
 * and let it be garbage-collected afterwards.
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
     * @return Hints\Module[]
     */
    protected function reflectModules(): array {
        $modules = [];

        return array_map(
            fn(BaseModule $module) => $this->reflectModule($module),
            $this->project_modules);
    }

    protected function reflectModule(BaseModule $module): \stdClass|Hints\Module
    {
        return (object)[
            'name' => $module->name,
            'path' => $module->path,
            'namespace' => $module->namespace,
            'tables' => [],
        ];
    }

    /**
     * @param Hints\Module[] $modules
     */
    protected function reflectTables(array &$modules): void {
        global $osm_app; /* @var App $osm_app */

        $tableClassNames = get_descendant_classes_by_name(
            TableDef::class);
            
        foreach ($tableClassNames as $name => $tableClassName) {
            $recordClassName = $this->getRecordClassName($tableClassName);

            $this->reflectRecordClass($modules, $name,
                $osm_app->classes[$recordClassName]);
        }
    }

    /**
     * @param Hints\Module[] $modules
     * @param string $tableName
     * @param Class_ $recordClass
     */
    protected function reflectRecordClass(array &$modules,
        string $tableName, Class_ $recordClass): void
    {
        global $osm_app; /* @var App $osm_app */

        foreach ($recordClass->properties as $property) {
            $this->reflectRecordProperty($modules, $tableName, $recordClass,
                $property);
        }

        $typeClassNames = get_descendant_classes_by_name($recordClass->name);

        foreach ($typeClassNames as $name => $typeClassName) {
            $this->reflectRecordTypeClass($modules, $tableName, $recordClass,
                $name, $osm_app->classes[$typeClassName]);
        }
    }

    /**
     * @param Hints\Module[] $modules
     * @param string $tableName
     * @param Class_ $recordClass
     * @param string $typeName
     * @param Class_ $typeClass
     */
    protected function reflectRecordTypeClass(array &$modules, string $tableName,
        Class_ $recordClass, string $typeName, Class_ $typeClass): void
    {
        foreach ($typeClass->properties as $property) {
            $this->reflectRecordTypeProperty($modules, $tableName,
                $typeName, $typeClass, $property);
        }
    }

    /**
     * @param Hints\Module[] $modules
     * @param string $tableName
     * @param Class_ $recordClass
     * @param Property $property
     */
    protected function reflectRecordProperty(array &$modules,
        string $tableName, Class_ $recordClass, Property $property): void
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

        $this->addProperty($modules, $tableName, $moduleClassName, $recordClass,
            $property);
    }

    /**
     * @param Hints\Module[] $modules
     * @param string $tableName
     * @param string $moduleClassName
     * @param Class_ $recordClass
     * @param Property $property
     */
    protected function addProperty(array $modules, string $tableName,
        string $moduleClassName, Class_ $recordClass, Property $property): void
    {
        if (!($module = $modules[$moduleClassName] ?? null)) {
            throw new NotSupported(__(
                "Property ':property' not defined in the project, but in ':module'", [
                    'property' => "{$property->class_name}::\${$property->name}",
                    'module' => $moduleClassName,
                ]));
        }

        if (!isset($module->tables[$tableName])) {
            $module->tables[$tableName] = (object)[
                'name' => $tableName,
                'alters' => $moduleClassName != $recordClass->module_class_name,
                'properties' => [],
            ];
        }

        $table = $module->tables[$tableName];


        throw new NotImplemented($this);
    }

    protected function reflectRecordTypeProperty(array &$modules,
        string $tableName, string $typeName, Class_ $typeClass,
        Property $property): void
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