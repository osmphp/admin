<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $condition #[Serialized]
 * @property Formula $then #[Serialized]
 * @property Formula $else_ #[Serialized]
 */
class Ternary extends Formula
{
    public $type = self::TERNARY;

    protected function get_condition(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_then(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_else_(): Formula {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->condition->parent = $this;
        $this->then->parent = $this;
        $this->else_->parent = $this;
    }
}