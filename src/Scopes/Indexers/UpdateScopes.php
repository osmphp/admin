<?php

namespace Osm\Admin\Scopes\Indexers;

use Osm\Admin\Base\Attributes\On;
use Osm\Admin\Tables\Indexer\UpdateTree;

#[On\Saving('scopes'), On\Saved('scopes', alias: 'parent')]
class UpdateScopes extends UpdateTree
{
    protected function index_level(?int $parent__level): int {
        return $parent__level !== null ? $parent__level + 1 : 0;
    }

    protected function index_id_path(?string $parent__id_path, int $id): string {
        return $parent__id_path !== null ? "{$parent__id_path}/{$id}" : "{$id}";
    }
}