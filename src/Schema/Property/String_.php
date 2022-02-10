<?php

namespace Osm\Admin\Schema\Property;
use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property ?int $max_length #[Serialized]
 * @property string $size #[Serialized]
 *
 * @uses Serialized
 */
#[Type('string')]
class String_ extends Scalar
{
    const VARCHAR_LENGTH = 255;

    protected function get_max_length(): ?int {
        return null;
    }

    protected function get_size(): string {
        return static::SMALL;
    }

    public function create(Blueprint $table): void {
        if ($this->max_length && $this->max_length <= static::VARCHAR_LENGTH) {
            $column = $table->string($this->name, $this->max_length);
        }
        else {
            $column = match ($this->size) {
                static::TINY => $table->tinyText($this->name),
                static::SMALL => $table->text($this->name),
                static::MEDIUM => $table->mediumText($this->name),
                static::LONG => $table->longText($this->name),
            };
        }

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}