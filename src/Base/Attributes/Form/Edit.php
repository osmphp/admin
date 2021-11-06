<?php

namespace Osm\Admin\Base\Attributes\Form;

use Osm\Admin\Base\Attributes\Markers\Form;
use Osm\Framework\Areas\Admin;

#[\Attribute(\Attribute::TARGET_CLASS), Form('edit')]
final class Edit
{
    public function __construct(
        public string $url,
        public string $title,
        public string $area_class_name = Admin::class,
    )
    {
    }
}