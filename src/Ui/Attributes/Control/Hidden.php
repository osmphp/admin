<?php

namespace Osm\Admin\Ui\Attributes\Control;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Ui\Control;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Hidden extends Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void
    {
        $data->control = null;
    }
}