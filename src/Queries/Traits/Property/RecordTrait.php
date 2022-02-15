<?php

namespace Osm\Admin\Queries\Traits\Property;

use Osm\Admin\Queries\Traits\PropertyTrait;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Property\Record::class)]
trait RecordTrait
{
    use PropertyTrait;

    public function insert(array &$inserts, mixed $value): void
    {
        /* @var Property|Property\Record|static $this */

        $inserts["{$this->name}_id"] = $value;
    }
}