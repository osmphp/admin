<?php

namespace Osm\Admin\Schema;

use Illuminate\Support\Str;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $name #[Serialized]
 * @property mixed $value #[Serialized]
 * @property string $title #[Serialized]
 * @property int|string $index #[Serialized]
 *
 * @uses Serialized
 */
class Option extends Object_
{
    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_value(): mixed {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        return Str::title($this->name);
    }

    protected function get_index(): string {
        return is_bool($this->value) ? (int)$this->value : $this->value;
    }

    public function get(): array {
        $class = new \ReflectionClass($this->__class->name);

        $options = [];

        foreach ($class->getReflectionConstants() as $constant) {
            $option = static::new([
                'name' => $constant->getName(),
                'value' => $constant->getValue(),
            ]);

            foreach ($constant->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();

                if ($instance instanceof Attribute) {
                    $instance->parse($option);
                }
            }

            $options[$option->index] = $option;
        }

        return $options;
    }
}