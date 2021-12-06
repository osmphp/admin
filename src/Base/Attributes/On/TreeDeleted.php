<?php

namespace Osm\Admin\Base\Attributes\On;

use Osm\Admin\Base\Attributes\Markers\On;

#[\Attribute(\Attribute::TARGET_CLASS), On('tree_deleted')]
final class TreeDeleted
{
    public function __construct(public string $table) { }
}