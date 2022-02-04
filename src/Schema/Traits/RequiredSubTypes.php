<?php

namespace Osm\Admin\Schema\Traits;

use Osm\Core\Exceptions\Required;
use Osm\Core\Traits\SubTypes;

trait RequiredSubTypes
{
    use SubTypes { get_type as get_optional_type; }

    protected function get_type(): ?string
    {
        return $this->get_optional_type()
            ?? throw new Required(__METHOD__);
    }
}