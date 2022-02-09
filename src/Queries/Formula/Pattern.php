<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $value #[Serialized]
 * @property Formula $pattern #[Serialized]
 *
 * @uses Serialized
 */
class Pattern extends Formula
{
    protected function get_value(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_pattern(): Formula {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->value->parent = $this;
        $this->pattern->parent = $this;
    }
}