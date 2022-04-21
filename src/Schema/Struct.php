<?php

namespace Osm\Admin\Schema;

use Illuminate\Support\Str;
use Osm\Admin\Schema\Attributes\Class_;
use Osm\Admin\Schema\Traits\AttributeParser;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Admin\Ui\Attributes\View;
use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Traits\StructTrait;
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
 * @property ?string $rename #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string[]|null $type_class_names #[Serialized]
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
 * @property string $s_no_objects #[Serialized]
 * @property string $s_title_and_n_more_object_s #[Serialized]
 *
 * @property List_[] $list_views #[Serialized]
 *
 * @uses Serialized
 */
class Struct extends Object_
{
    use RequiredSubTypes, AttributeParser;

    public const SCHEMA_PROPERTY = null;
    public const ROOT_CLASS_NAME = null;

    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): CoreClass {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->classes[$this->schema->getVersionedName($this->name)];
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_properties(): array {
        throw new Required(__METHOD__);
    }

    protected function get_type_class_names(): ?array {
        return $this->parseTypeClassNames($this->reflection);
    }

    public function parseTypeClassNames(CoreClass $reflection): array
    {
        global $osm_app; /* @var App $osm_app */

        $types = [];

        /* @var Type $type */
        if ($type = $reflection->attributes[Type::class] ?? null) {
            $types[$type->name] = $reflection->name;
        }

        foreach ($reflection->child_class_names as $childClassName) {
            $types = array_merge($types, $this->parseTypeClassNames(
                $osm_app->classes[$childClassName]));
        }

        ksort($types);
        return $types;
    }

    protected function get_instance(): Object_ {
        throw new NotImplemented($this);
    }

    public function __wakeup(): void
    {
        foreach ($this->properties as $property) {
            $property->parent = $this;
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

    protected function get_s_no_objects(): string {
        return "No {$this->s_objects_lowercase}.";
    }

    protected function get_s_title_and_n_more_object_s(): string {
        return ":title And :count More {$this->s_object_s}";
    }

    public function parse(): void {
        $this->parseAttributes();

        $this->properties = [];

        $this->parsePropertiesRecursively($this->reflection);

        foreach ($this->properties as $property) {
            $property->parse();
        }
    }

    protected function parsePropertiesRecursively(CoreClass $reflection): void {
        global $osm_app; /* @var App $osm_app */

        /* @var ?Type $type */
        $type = $reflection->attributes[Type::class] ?? null;
        $typeName = $type?->name;

        foreach ($reflection->properties as $property) {
            $this->parseProperty($property, $reflection);
        }

        foreach ($reflection->child_class_names as $childClassName) {
            $this->parsePropertiesRecursively(
                $osm_app->classes[$childClassName]);
        }
    }

    protected function parseProperty(CoreProperty $reflection,
        CoreClass $classReflection): void
    {
        if ($reflection->name == '__class') {
            return;
        }

        $types = $this->schema->parseTypes($classReflection,
            static::ROOT_CLASS_NAME);

        if (isset($this->properties[$reflection->name])) {
            $this->properties[$reflection->name]->parseTypeSpecificFormulas(
                $types, $reflection);
            return;
        }

        $new = "{$this->propertyClassName($reflection)}::new";

        $this->properties[$reflection->name] = $property = $new([
            'parent' => $this,
            'name' => $reflection->name,
            'reflection' => $reflection,
            'if' => $types ? ['type' => $types] : [],
        ]);

        $property->parse();
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

    protected function get_list_views(): array {
        global $osm_app; /* @var App $osm_app */

        /* @var Table|static $this */

        $views = [];

        $classes = $osm_app->descendants->classes(List_::class);
        foreach ($classes as $class) {
            /* @var Class_ $classAttribute */
            if (!($classAttribute = $class->attributes[Class_::class] ?? null)) {
                continue;
            }

            if ($classAttribute->class_name != $this->name) {
                continue;
            }

            /* @var View $viewAttribute */
            if (!($viewAttribute = $class->attributes[View::class] ?? null)) {
                continue;
            }

            if ($viewAttribute->name !== 'list') {
                continue;
            }

            $new = "{$class->name}::new";
            $name = Str::snake(mb_substr($class->name,
                mb_strrpos($class->name, '\\') + 1));

            $views[$name] = $new([
                'struct' => $this,
                'name' => $name,
            ]);
        }

        if (!isset($views['grid'])) {
            $views['grid'] = List_\Grid::new([
                'struct' => $this,
                'name' => 'grid',
            ]);
        }

        return $views;
    }
}