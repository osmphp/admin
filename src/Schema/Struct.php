<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Schema\Traits\AttributeParser;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Property as CoreProperty;
use Osm\Core\Traits\SubTypes;
use function Osm\__;

/**
 * @property Schema $schema
 * @property CoreClass $reflection
 * @property string $name #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string[] $type_class_names #[Serialized]
 * @property Object_ $instance
 *
 * @property string $s_object #[Serialized]
 * @property string $s_objects #[Serialized]
 * @property string $s_object_lowercase #[Serialized]
 * @property string $s_objects_lowercase #[Serialized]
 * @property string $s_object_s #[Serialized]
 * @property string $s_object_s_lowercase #[Serialized]
 * @property string $s_new_object #[Serialized]
 * @property string $s_saving_new_object #[Serialized]
 * @property string $s_new_object_saved #[Serialized]
 * @property string $s_n_objects #[Serialized]
 * @property string $s_object_id #[Serialized]
 * @property string $s_n_m_objects_selected #[Serialized]
 * @property string $s_deleting_n_objects #[Serialized]
 * @property string $s_n_objects_deleted #[Serialized]
 *
 * @uses Serialized
 */
class Struct extends Object_
{
    use RequiredSubTypes, AttributeParser;

    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): CoreClass {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->classes[$this->name];
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_properties(): array {
        throw new Required(__METHOD__);
    }

    protected function get_type_class_names(): array {
        return [];
    }

    protected function get_instance(): Object_ {
        throw new NotImplemented($this);
    }

    public function __wakeup(): void
    {
        foreach ($this->properties as $property) {
            $property->struct = $this;
        }
    }

    protected function get_s_object(): string {
        $segments = explode('\\', $this->reflection->name);
        return $segments[count($segments) - 1];
    }

    protected function get_s_objects(): string {
        return "{$this->s_object}s";
    }

    protected function get_s_object_lowercase(): string {
        return mb_strtolower($this->s_object);
    }

    protected function get_s_objects_lowercase(): string {
        return mb_strtolower($this->s_objects);
    }

    protected function get_s_object_s(): string {
        return "{$this->s_object}(s)";
    }

    protected function get_s_object_s_lowercase(): string {
        return mb_strtolower($this->s_object_s);
    }

    protected function get_s_new_object(): string {
        return "New {$this->s_object}";
    }

    protected function get_s_saving_new_object(): string {
        return "Saving new {$this->s_object_lowercase} ...";
    }

    protected function get_s_new_object_saved(): string {
        return "New {$this->s_object_lowercase} saved successfully.";
    }

    protected function get_s_n_objects(): string {
        return ":count {$this->s_objects}";
    }

    protected function get_s_object_id(): string {
        return "{$this->s_object} #:id";
    }

    protected function get_s_n_m_objects_selected(): string {
        return ":selected / :count {$this->s_object_s_lowercase} selected";
    }

    protected function get_s_deleting_n_objects(): string {
        return "Deleting :selected {$this->s_object_s_lowercase} ...";
    }

    protected function get_s_n_objects_deleted(): string {
        return ":selected {$this->s_object_s_lowercase} deleted.";
    }

    public function parse(): void {
        $this->parseAttributes();

        $this->properties = [];

        $this->parsePropertiesRecursively($this->reflection);

        foreach ($this->properties as $property) {
            $property->parseAttributes();
        }
    }

    protected function parsePropertiesRecursively(CoreClass $reflection,
        bool $base = true): void
    {
        global $osm_app; /* @var App $osm_app */

        /* @var ?Type $type */
        $type = $reflection->attributes[Type::class] ?? null;
        $typeName = $type?->name;

        foreach ($reflection->properties as $property) {
            $this->parseProperty($property, $reflection, $base);
        }

        foreach ($reflection->child_class_names as $childClassName) {
            $this->parsePropertiesRecursively(
                $osm_app->classes[$childClassName], false);
        }
    }

    protected function parseProperty(CoreProperty $reflection,
        CoreClass $classReflection, bool $base): void
    {
        if ($reflection->name == '__class') {
            return;
        }

        $new = "{$this->propertyClassName($reflection)}::new";
        $types = $base ? null : $this->parseTypes($classReflection);

        $property = $new([
            'class' => $this,
            'name' => $reflection->name,
            'reflection' => $reflection,
            'type_specific' => $types,
        ]);

        if (!isset($this->properties[$reflection->name])) {
            $this->properties[$reflection->name] = $property;
            $property->parse();
        }
        else {
            $this->properties[$reflection->name]->parseTypeSpecificAttributes(
                $types, $reflection);
        }
    }

    protected function propertyBelongs(CoreProperty $reflection): bool
    {
        throw new NotImplemented($this);
    }

    protected function propertyClassName(CoreProperty $reflection): string
    {
        global $osm_app; /* @var App $osm_app */

        if (is_a($reflection->type, \DateTime::class, true)) {
            return Property\DateTime::class;
        }

        if (is_subclass_of($reflection->type, Record::class, true)) {
            return Property\Record::class;
        }

        if (is_subclass_of($reflection->type, Object_::class, true)) {
            return Property\Object_::class;
        }

        return match ($reflection->type) {
            null, 'mixed' => Property\Mixed_::class,
            'int' => Property\Int_::class,
            'string' => Property\String_::class,
            'float' => Property\Float_::class,
            'bool' => Property\Bool_::class,
            default => throw new NotSupported(__("Type ':type', used in ':class::\$:property' is not supported.", [
                'type' => $reflection->type,
                'class' => $reflection->class->name,
                'property' => $reflection->name,
            ])),
        };
    }

    protected function parseTypes(CoreClass $reflection): array
    {
        global $osm_app; /* @var App $osm_app */

        $types = [];

        /* @var Type $type */
        if ($type = $reflection->attributes[Type::class] ?? null) {
            $types[] = $type->name;
        }

        foreach ($reflection->child_class_names as $childClassName) {
            $types = array_merge($types, $this->parseTypes(
                $osm_app->classes[$childClassName]));
        }

        return array_unique($types);
    }

}