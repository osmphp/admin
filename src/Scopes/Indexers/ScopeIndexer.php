<?php

namespace Osm\Admin\Scopes\Indexers;

use Osm\Admin\Base\Attributes\Indexer\Source;
use Osm\Admin\Base\Attributes\Indexer\Target;
use Osm\Admin\Indexing\Indexer;
use Osm\Admin\Scopes\Scope;
use function Osm\query;

#[Target(Scope::class), Source(Scope::class)]
class ScopeIndexer extends Indexer
{
    protected function index_level(?Scope $parent): int {
        return $parent->level ?? 0;
    }

    protected function index_id_path(?Scope $parent, int $id): string {
        return $parent ? "{$parent->id_path}/{$id}" : "{$id}";
    }

    protected function index_parent(?int $parent_id): ?Scope {
        return $parent_id
            ? query(Scope::class)
                ->where('id', $parent_id)
                ->hydrate()
                ->first(['level', 'id_path'])
            : null;
    }
}