<?php

namespace Osm\Admin\Base\Attributes\Interface_;

use Osm\Admin\Base\Attributes\Markers\Interface_;

#[\Attribute(\Attribute::TARGET_CLASS), Interface_('admin')]
final class Admin
{
    public function __construct(
        public string $url,
    )
    { }
}