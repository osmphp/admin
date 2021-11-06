<?php

namespace Osm\Admin\Base\Attributes;

use Osm\Framework\Areas\Admin;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Icon
{
    public function __construct(
        public string $url,
        public string $title,
        public string $area_class_name = Admin::class,
    )
    {
    }
}