<?php

namespace Osm\Admin\Base\Attributes\Form;

use Osm\Admin\Base\Attributes\Markers\Form\Chapter as ChapterMarker;

#[\Attribute(\Attribute::TARGET_PROPERTY), ChapterMarker('standard')]
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