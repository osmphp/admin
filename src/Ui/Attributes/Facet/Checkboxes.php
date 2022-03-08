<?php

namespace Osm\Admin\Ui\Attributes\Facet;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Ui\Facet;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Checkboxes extends Attribute
{
    public function parse(\stdClass|Struct|Property|Option $data): void
    {
        $data->facet = Facet\Checkboxes::new();
    }
}