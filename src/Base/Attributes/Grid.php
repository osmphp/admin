<?php

namespace Osm\Admin\Base\Attributes;

use Osm\Admin\Base\Attributes\Markers\Grid as GridMarker;
use Osm\Framework\Areas\Admin;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), GridMarker('page')]
final class Grid
{
    public function __construct(
        public string $url,
        public string $title,
        public string $area_class_name = Admin::class,
        public array $select = ['id'],
        public ?string $parameters = null,
        public bool $multiselect = true,
        public bool $editable = false,
        public bool $can_create = true,
    )
    {
    }
}