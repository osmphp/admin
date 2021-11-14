<?php

namespace Osm\Admin\Scopes\Attributes\Storage;

use Osm\Admin\Base\Attributes\Markers\Storage;
use Osm\Admin\Scopes\Scopes as ScopeQuery;

#[\Attribute(\Attribute::TARGET_CLASS), Storage('scopes')]
final class Scopes
{
    public function __construct(
        public string $name = 'scopes',
        public int $version = 1,
        public ?string $query_class_name = ScopeQuery::class,
    )
    {
    }
}