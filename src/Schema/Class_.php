<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Property as CoreProperty;
use Osm\Core\Traits\SubTypes;

/**
 * @property Schema $schema
 * @property CoreClass $reflection
 * @property string $name #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string[] $type_class_names #[Serialized]
 * @property Class_\Type[] $types
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
 */
class Class_ extends Object_
{
    use SubTypes;

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

    protected function get_types(): array {
        throw new NotImplemented($this);
    }

    protected function get_instance(): Object_ {
        throw new NotImplemented($this);
    }

    public function __wakeup(): void
    {
        foreach ($this->properties as $property) {
            $property->class = $this;
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

        foreach ($this->reflection->properties as $reflection) {
            $this->parseProperty($reflection);
        }

        foreach ($this->properties as $property) {
            $property->parse();
        }
    }

    protected function parseAttributes(): void
    {
    }

    protected function parseProperty(CoreProperty $reflection): void
    {
        if (isset($this->properties[$reflection->name])) {
            return;
        }

        if (!$this->propertyBelongs($reflection)) {
            return;
        }

        $new = "{$this->propertyClassName($reflection)}::new";

        $this->properties[$reflection->name] = $new([
            'class' => $this,
            'name' => $reflection->name,
            'reflection' => $reflection,
        ]);
    }

    protected function propertyBelongs(CoreProperty $reflection): bool
    {
        throw new NotImplemented($this);
    }

    protected function propertyClassName(CoreProperty $reflection): string
    {
        throw new NotImplemented($this);
    }
}