<?php

namespace Osm\Admin\Base\Attributes\Indexer;

use Osm\Admin\Base\Attributes\Markers\IndexerSource;

#[
    \Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE),
    IndexerSource('from'),
]
final class From
{
    public function __construct(public string $table, public string $name) {
    }
}