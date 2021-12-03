<?php

namespace Osm\Admin\Base\Attributes\Indexer;

use Osm\Admin\Base\Attributes\Markers\IndexerSource;

#[\Attribute(\Attribute::TARGET_CLASS), IndexerSource('to')]
final class To
{
    public string $name = 'this';

    public function __construct(
        public string $table,
        public ?string $type_name = null,
    ) { }
}