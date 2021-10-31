<?php

namespace Osm\Data\Tables\Column;

use Osm\Data\Tables\Column;
use Osm\Core\Attributes\Serialized;

/**
 * @property bool $unsigned #[Serialized]
 * @property ?string $references #[Serialized]
 * @property ?string $references_table #[Serialized]
 * @property ?string $references_column #[Serialized]
 * @property ?string $on_delete #[Serialized]
 */
class Int_ extends Column
{
    protected function get_references_table(): ?string {
        return $this->references
            ? substr($this->references, 0,
                strpos($this->references, '.'))
            : null;
    }

    protected function get_references_column(): ?string {
        return $this->references
            ? substr($this->references,
                strpos($this->references, '.') + 1)
            : null;
    }
}