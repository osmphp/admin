<?php

namespace Osm\Admin\Schema\Attributes\Formula;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;

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