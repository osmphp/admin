<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;

/**
 * Adds the property to faceted navigation in the sidebar
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Faceted extends Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void
    {
        $data->faceted = true;
    }
}