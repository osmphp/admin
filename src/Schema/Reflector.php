<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Base\Attributes\Markers\Storage as ObjectMarker;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
use Osm\Admin\Queries\Query;
use Osm\Framework\Cache\Descendants;

/**
 * @property Schema $schema
 * @property Class_[] $classes
 * @property Descendants $descendants
 * @property CoreClass[] $core_classes
 * @property string[] $data_class_markers
 */
class Reflector extends Object_
{
    public function getClasses(): array {
        $this->classes = [];

        foreach ($this->core_classes as $class) {
            $this->getClass($class);
        }

        return $this->classes;
    }

    protected function getClass(CoreClass $reflection): Class_ {
        if (isset($this->classes[$reflection->name])) {
            return $this->classes[$reflection->name];
        }

        $this->classes[$reflection->name] = $class = Class_::new([
            'name' => $reflection->name,
            'reflection' => $reflection,
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
        global $osm_app; /* @var App $osm_app */

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
            $this->getClass($osm_app->classes[$reflection->type]);
        }
    }

    protected function getSubtypes(Class_ $class): void {
        global $osm_app; /* @var App $osm_app */

        $class->types = array_map(
            fn(string $className) => $this->getClass($osm_app->classes[$className]),
            $class->reflection->types ?? []);

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

    protected function get_core_classes(): array {
        global $osm_app; /* @var App $osm_app */

        return array_filter($osm_app->classes,
            fn(CoreClass $class) => $this->isDataClass($class));
    }

    protected function isDataClass(CoreClass $class): bool
    {
        foreach ($this->data_class_markers as $attributeClassName) {
            if (isset($class->attributes[$attributeClassName])) {
                return true;
            }
        }

        return false;
    }

    protected function get_data_class_markers(): array {
        global $osm_app; /* @var App $osm_app */

        $dataClassMarkers = [];

        foreach ($osm_app->classes as $class) {
            if (!isset($class->attributes[\Attribute::class])) {
                continue;
            }

            if (!isset($class->attributes[ObjectMarker::class])) {
                continue;
            }

            $dataClassMarkers[] = $class->name;
        }

        return $dataClassMarkers;
    }
}