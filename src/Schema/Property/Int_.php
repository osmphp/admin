<?php

namespace Osm\Admin\Schema\Property;
use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property bool $unsigned #[Serialized]
 * @property string $size #[Serialized]
 * @property bool $auto_increment #[Serialized]
 *
 * @uses Serialized
 */
#[Type('int')]
class Int_ extends Scalar
{
    protected function get_unsigned(): bool {
        return false;
    }

    protected function get_size(): string {
        return static::MEDIUM;
    }

    protected function get_auto_increment(): bool {
        return false;
    }

    public function create(Blueprint $table): void {
        $column = match ($this->size) {
            static::TINY => $table->tinyInteger($this->name),
            static::SMALL => $table->smallInteger($this->name),
            static::MEDIUM => $table->integer($this->name),
            static::LONG => $table->bigInteger($this->name),
        };

        if ($this->unsigned || $this->auto_increment) {
            $column->unsigned();
        }

        if ($this->auto_increment) {
            $column->autoIncrement();
        }

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}