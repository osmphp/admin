<?php

namespace Osm\Admin\Schema\Attributes;

use Osm\Admin\Schema\Attribute;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Table extends Attribute
{
    public function __construct(public string $name)
    {
    }

    public function parse(\stdClass|Struct|Property $data): void {
        $data->table_name = $this->name;
    }
}