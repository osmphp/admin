<?php

namespace Osm\Admin\Base\Attributes\Indexer;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Source
{
    public function __construct(
        public string $class_name,
    )
    {
    }
}