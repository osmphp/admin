<?php

namespace Osm\Admin\Base\Attributes\On;

use Osm\Admin\Base\Attributes\Markers\On;

#[\Attribute(\Attribute::TARGET_CLASS), On('saving')]
final class Saving
{
    public function __construct(
        public string $table,
        public ?string $type_name = null,
    ) { }
}