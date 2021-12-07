<?php

namespace Osm\Admin\Base\Attributes\Form;

use Osm\Admin\Base\Attributes\Markers\Form\Field;

#[\Attribute(\Attribute::TARGET_PROPERTY), Field('int')]
final class Int_
{
    public function __construct(
        public int $sort_order,
        public string $title,
        public string $group_name = 'implicit',
    )
    {
    }
}