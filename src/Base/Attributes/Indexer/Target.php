<?php

namespace Osm\Admin\Base\Attributes\Indexer;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Target
{
    public function __construct(
        public string $class_name,
    )
    {
    }
}