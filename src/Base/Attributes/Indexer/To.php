<?php

namespace Osm\Admin\Base\Attributes\Indexer;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class To
{
    public function __construct(
        public string $name,
        public ?string $type_name = null,
    ) { }
}