<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Option as SchemaOption;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

/**
 * Property only accepts values defined in the specified option class.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Option extends Attribute
{
    public function __construct(public string $class_name)
    {
    }

    public function parse(\stdClass|Struct|Property|SchemaOption $data): void {
        $data->option_class_name = $this->class_name;
    }
}