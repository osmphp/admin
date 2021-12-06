<?php

namespace Osm\Admin\Base\Attributes\On;

use Osm\Admin\Base\Attributes\Markers\On;

#[\Attribute(\Attribute::TARGET_CLASS), On('subtree_deleted')]
final class SubtreeDeleted
{
    public function __construct(
        public string $table,
        public string $name,
    ) { }
}