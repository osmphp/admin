<?php

namespace Osm\Admin\Base\Attributes\Form;

use Osm\Admin\Base\Attributes\Markers\Form\Section as SectionMarker;

#[\Attribute(\Attribute::TARGET_PROPERTY), SectionMarker('standard')]
final class Section
{
    public function __construct(
        public int $sort_order,
        public string $name,
        public string $title,
    )
    {
    }
}