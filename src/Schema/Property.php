<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
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
 * @property ?string $formula #[Serialized]
 * @property bool $virtual #[Serialized]
 * @property bool $computed #[Serialized]
 * @property bool $overridable #[Serialized]
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

    protected function get_computed(): bool {
        return false;
    }

    public function parse(): void {
        throw new NotImplemented($this);
    }
}