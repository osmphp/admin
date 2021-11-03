<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
use Osm\Admin\Base\Attributes\Type;
use Osm\Admin\Queries\Attributes\Of;
use Osm\Admin\Queries\Query;
use Osm\Framework\Cache\Descendants;

/**
 * @property Schema $schema
 * @property Class_[] $classes
 * @property Descendants $descendants
 */
class Reflector extends Object_
{
    public function getClasses(): array {
        $this->classes = [];

        $queryClasses = $this->descendants->classes(Query::class);

        foreach ($queryClasses as $queryClass) {
            /* @var Of $of */
            if ($of = $queryClass->attributes[Of::class] ?? null) {
                $this->getClass($of->class_name);
            }
        }

        return $this->classes;
    }

    protected function getClass(string $className): Class_ {
        global $osm_app; /* @var App $osm_app */

        if (isset($this->classes[$className])) {
            return $this->classes[$className];
        }

        $this->classes[$className] = $class = Class_::new([
            'name' => $className,
            'reflection' => $osm_app->classes[$className],
            'schema' => $this->schema,
        ]);

        $this->getProperties($class);

        $this->getSubtypes($class);

        return $class;
    }

    protected function getProperties(Class_ $class): void
    {
        $class->properties = [];

        foreach ($class->reflection->properties as $property) {
            $this->getProperty($class, $property);
        }
    }

    protected function getProperty(Class_ $class, CoreProperty $reflection)
        : void
    {
        if (!isset($reflection->attributes[Serialized::class])) {
            return;
        }

        $moduleClassName = is_subclass_of($class->reflection->name,
            $reflection->class_name, true)
                ? $class->reflection->module_class_name
                : $reflection->module_class_name;

        if (!$moduleClassName) {
            return;
        }

        $class->properties[$reflection->name] = Property\Regular::new([
            'name' => $reflection->name,
            'class_name' => $class->name,
            'class' => $class,
            'module_class_name' => $moduleClassName,
            'reflection' => $reflection,
        ]);

        if ($reflection->type &&
            is_subclass_of($reflection->type, Object_::class,
                true))
        {
            $this->getClass($reflection->type);
        }
    }

    protected function getSubtypes(Class_ $class): void {
        $class->type_class_names = $this->descendants->byName($class->name,
            Type::class);

        $class->types = array_map(
            fn(string $className) => $this->getClass($className),
            $class->type_class_names);

        foreach ($class->types as $typeName => $type) {
            $this->mergeProperties($class, $typeName, $type);
        }
    }

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }

    protected function mergeProperties(Class_ $class, string $typeName,
        Class_ $type): void
    {
        foreach ($type->properties as $property) {
            $this->mergeProperty($class, $typeName, $type, $property);
        }
    }

    protected function mergeProperty(Class_ $class, string $typeName,
        Class_ $type, Property $typeProperty): void
    {
        if (!isset($class->properties[$typeProperty->name])) {
            $class->properties[$typeProperty->name] = Property\TypeSpecific::new([
                'name' => $typeProperty->name,
                'class_name' => $class->name,
                'class' => $class,
                'type_names' => [$typeName],
                'types' => [$type],
            ]);
        }

        $classProperty = $class->properties[$typeProperty->name];

        if (!$classProperty->type != 'type_specific') {
            return;
        }

        $classProperty->type_names[] = $typeName;
        $classProperty->types[] = $type;
    }
}