<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
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
    use RequiredSubTypes;

    public const TINY = 'tiny';
    public const SMALL = 'small';
    public const MEDIUM = 'medium';
    public const LONG = 'long';

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): CoreProperty {
        return $this->class->reflection->properties[$this->name];
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_nullable(): bool {
        return false;
    }

    protected function get_array(): bool {
        return false;
    }

    protected function get_explicit(): bool {
        return false;
    }

    protected function get_virtual(): bool {
        return false;
    }

    protected function get_control_type(): string {
        return 'input';
    }

    protected function get_before(): array {
        return [];
    }

    protected function get_after(): array {
        return [];
    }

    protected function get_in(): string {
        return '///';
    }
}