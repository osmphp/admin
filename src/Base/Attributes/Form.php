<?php

namespace Osm\Admin\Base\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Form
{
    public function __construct(
        public string $url,
        public ?string $title_create = null,
        public ?string $title_edit = null,
    )
    {
    }
}