<?php

namespace Osm\Admin\Base\Attributes\Interface_\Table;

use Osm\Admin\Base\Attributes\Markers\Interface_;

#[\Attribute(\Attribute::TARGET_CLASS), Interface_('table_admin')]
final class Admin
{
    public function __construct(
        public string $url,
        public string $s_object,
        public ?string $s_objects = null,
        public ?string $s_new_object = null,
    )
    { }
}