<?php

namespace Osm\Admin\Base\Attributes\Form;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Create
{
    public function __construct(
        public string $title,
        public string $url = '/create',
    )
    {
    }
}