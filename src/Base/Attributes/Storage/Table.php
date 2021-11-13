<?php

namespace Osm\Admin\Base\Attributes\Storage;

use Osm\Admin\Base\Attributes\Markers\Storage;

#[\Attribute(\Attribute::TARGET_CLASS), Storage('table')]
final class Table
{
    public function __construct(
        public string $name,
        public ?string $query_class_name = null,
    )
    {
    }
}