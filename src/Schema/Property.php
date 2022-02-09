<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Schema\Traits\AttributeParser;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
use Osm\Core\Attributes\Serialized;

/**
 * @property Struct $struct
 * @property CoreProperty $reflection
 * @property string $name #[Serialized]
 * @property string[]|null $type_specific #[Serialized]
 * @property \stdClass[] $type_specific_settings #[Serialized]
 * @property bool $nullable #[Serialized]
 * @property bool $array #[Serialized]
 * @property bool $explicit #[Serialized]
 * @property ?string $formula #[Serialized]
 * @property bool $virtual #[Serialized]
 * @property bool $computed #[Serialized]
 * @property bool $overridable #[Serialized]
 *
 * @uses Serialized
 */
class Property extends Object_
{
    use RequiredSubTypes, AttributeParser;

    public const TINY = 'tiny';
    public const SMALL = 'small';
    public const MEDIUM = 'medium';
    public const LONG = 'long';

    protected function get_struct(): Struct {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): CoreProperty {
        return $this->struct->reflection->properties[$this->name];
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_type_specific(): ?array {
        return null;
    }

    protected function get_type_specific_settings(): array {
        return [];
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
        $this->parseAttributes();
    }

    public function parseTypeSpecificAttributes(array $types,
        CoreProperty $reflection): void
    {
        $data = $this->parseAttributeData();

        $typeSpecificData = [];
        foreach (['formula'] as $propertyName) {
            if (isset($data->$propertyName)) {
                $typeSpecificData[$propertyName] = $data->$propertyName;
            }
        }

        if (empty($typeSpecificData)) {
            return;
        }

        foreach ($types as $type) {
            if (!isset($this->type_specific_settings[$type])) {
                $this->type_specific_settings[$type] = new \stdClass();
            }

            foreach ($typeSpecificData as $propertyName => $value) {
                $this->type_specific_settings[$type]->$propertyName = $value;
            }
        }
    }
}