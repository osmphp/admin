<?php

namespace Osm\Admin\Base\Attributes\On;

use Osm\Admin\Base\Attributes\Markers\On;

#[
    \Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE),
    On('saved'),
]
final class Saved
{
    public function __construct(public string $table, public string $name) {
    }
}