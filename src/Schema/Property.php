<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
use Osm\Core\Traits\SubTypes;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_ $class
 * @property CoreProperty $reflection
 * @property string $name #[Serialized]
 * @property bool $nullable #[Serialized]
 * @property bool $array #[Serialized]
 * @property bool $explicit #[Serialized]
 * @property bool $virtual #[Serialized]
 * @property ?string $formula #[Serialized]
 * @property bool $overridable #[Serialized]
 * @property string $control_type #[Serialized]
 * @property string[] $before #[Serialized]
 * @property string[] $after #[Serialized]
 * @property string $in #[Serialized]
 */
class Property extends Object_
{
    use SubTypes;

    protected function get_class(): Class_ {
        throw new NotImplemented($this);
    }

    protected function get_reflection(): CoreProperty {
        throw new NotImplemented($this);
    }

    protected function get_name(): string {
        throw new NotImplemented($this);
    }

    protected function get_nullable(): bool {
        throw new NotImplemented($this);
    }

    protected function get_array(): bool {
        throw new NotImplemented($this);
    }

    protected function get_explicit(): bool {
        throw new NotImplemented($this);
    }

    protected function get_virtual(): bool {
        throw new NotImplemented($this);
    }

    protected function get_control_type(): string {
        throw new NotImplemented($this);
    }

    protected function get_before(): array {
        throw new NotImplemented($this);
    }

    protected function get_after(): array {
        throw new NotImplemented($this);
    }

    protected function get_in(): string {
        throw new NotImplemented($this);
    }
}