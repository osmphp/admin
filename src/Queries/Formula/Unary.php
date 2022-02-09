<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $operand #[Serialized]
 *
 * @uses Serialized
 */
class Unary extends Formula
{
    protected function get_operand(): Formula {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->operand->parent = $this;
    }
}