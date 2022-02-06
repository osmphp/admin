<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;

/**
 * @property Formula $value #[Serialized]
 * @property Formula $from #[Serialized]
 * @property Formula $to #[Serialized]
 */
class Between extends Formula
{
    protected function get_value(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_from(): Formula {
        throw new Required(__METHOD__);
    }

    protected function get_to(): Formula {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        $this->value->parent = $this;
        $this->from->parent = $this;
        $this->to->parent = $this;
    }
}