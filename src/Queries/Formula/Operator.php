<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula[] $operands #[Serialized]
 * @property int[] $operators #[Serialized]
 */
class Operator extends Formula
{
    protected function get_operands(): array {
        throw new Required(__METHOD__);
    }

    protected function get_operators(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->operands as $operand) {
            $operand->parent = $this;
        }
    }
}