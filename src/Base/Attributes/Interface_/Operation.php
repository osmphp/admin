<?php

namespace Osm\Admin\Base\Attributes\Interface_;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Operation
{
    public function __construct(
        public string $name,
        public bool $enabled = true,
        public ?string $title = null,
    )
    { }
}