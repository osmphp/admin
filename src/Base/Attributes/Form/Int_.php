<?php

namespace Osm\Admin\Base\Attributes\Form;

use Osm\Admin\Base\Attributes\Markers\FormField;

#[\Attribute(\Attribute::TARGET_PROPERTY), FormField('int')]
final class Int_
{
    public function __construct(
        public int $sort_order,
        public string $title,
        public string $in = '//',
    )
    {
    }
}