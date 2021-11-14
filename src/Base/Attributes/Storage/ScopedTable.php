<?php

namespace Osm\Admin\Base\Attributes\Storage;

use Osm\Admin\Base\Attributes\Markers\Storage;

#[\Attribute(\Attribute::TARGET_CLASS), Storage('scoped_table')]
final class ScopedTable
{
    public function __construct(
        public string $name,
        public int $version = 1,
        public ?string $query_class_name = null,
    ) { }
}