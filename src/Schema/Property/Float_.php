<?php

namespace Osm\Admin\Schema\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;

/**
 * @property bool $unsigned #[Serialized]
 * @property int $precision #[Serialized]
 * @property int $scale #[Serialized]
 *
 * @uses Serialized
 */
#[Type('float')]
class Float_ extends Scalar
{
    protected function get_unsigned(): bool {
        return false;
    }

    protected function get_precision(): int {
        return 8;
    }

    protected function get_scale(): int {
        return 2;
    }

    public function create(Blueprint $table): void {
        $column = $table->decimal($this->name, total: $this->precision,
            places: $this->scale);

        if ($this->unsigned) {
            $column->unsigned();
        }

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}