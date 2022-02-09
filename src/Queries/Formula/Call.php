<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property string $function #[Serialized]
 * @property Formula[] $args #[Serialized]
 *
 * @uses Serialized
 */
class Call extends Formula
{
    public $type = self::CALL;

    protected function get_function(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_args(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->args as $arg) {
            $arg->parent = $this;
        }
    }
}