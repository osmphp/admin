<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $value #[Serialized]
 * @property Formula[] $items #[Serialized]
 *
 * @uses Serialized
 */
class In_ extends Formula
{
    protected function get_value(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_items(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->value->parent = $this;

        foreach ($this->items as $item) {
            $item->parent = $this;
        }
    }
}