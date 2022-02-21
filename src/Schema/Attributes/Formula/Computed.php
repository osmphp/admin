<?php

namespace Osm\Admin\Schema\Attributes\Formula;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Computed extends Attribute
{
    public function __construct(public string $formula)
    {
    }

    public function parse(\stdClass|Struct|Property $data): void {
        $data->computed = true;
        $data->overridable = false;
        $data->virtual = false;
        $data->formula = $this->formula;
    }
}