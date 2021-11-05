<?php

namespace Osm\Admin\Base\Attributes\Form;

#[\Attribute(\Attribute::TARGET_PROPERTY), Part('chapter')]
final class Chapter
{
    public function __construct(
        public int $sort_order,
        public string $name,
        public string $title,
    )
    {
    }
}