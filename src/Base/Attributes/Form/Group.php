<?php

namespace Osm\Admin\Base\Attributes\Form;

use Osm\Admin\Base\Attributes\Markers\Form\Group as GroupMarker;

#[\Attribute(\Attribute::TARGET_PROPERTY), GroupMarker('standard')]
final class Group
{
    public function __construct(
        public int $sort_order,
        public string $name,
        public string $title,
        public string $section_name = 'implicit',
    )
    {
    }
}