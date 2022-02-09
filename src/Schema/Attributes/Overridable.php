<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Overridable extends Attribute
{
    public function __construct(public string $formula)
    {
    }

    public function parse(\stdClass|Struct|Property $data): void {
        $data->overridable = true;
        $data->computed = false;
        $data->virtual = false;
        $data->formula = $this->formula;
    }
}