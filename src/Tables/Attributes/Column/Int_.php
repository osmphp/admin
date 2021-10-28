<?php

namespace Osm\Data\Tables\Attributes\Column;

use Osm\Data\Tables\Attributes\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Int_ extends Column
{
    public function __construct(
        public bool $unsigned = false,
        public ?string $references = null,
        public ?string $on_delete = null,
    )
    {
    }
}