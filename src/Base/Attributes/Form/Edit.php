<?php

namespace Osm\Admin\Base\Attributes\Form;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Edit
{
    public function __construct(
        public string $title,
        public string $url = '/edit',
    )
    {
    }
}