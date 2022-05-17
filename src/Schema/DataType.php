<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

class DataType extends Object_
{
    use RequiredSubTypes;

    public function __wakeup(): void
    {
    }

    public function castToSql(Formula $formula, array &$bindings,
        array &$from, string $join): string
    {
        throw new NotImplemented($this);
    }
}