<?php

namespace Osm\Admin\Schema\Property;

use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property string $on_delete #[Serialized]
 */
class Record extends Compound
{
    protected function get_on_delete(): string {
        throw new NotImplemented($this);
    }
}