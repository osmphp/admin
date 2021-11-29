<?php

namespace Osm\Admin\Scopes\Indexers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Base\Attributes\Indexer\Source;
use Osm\Admin\Base\Attributes\Indexer\Target;
use Osm\Admin\Scopes\Scope;
use Osm\Admin\Tables\TableIndexer;
use Osm\Core\Object_;
use function Osm\query;

#[Target('scopes'), Source('scopes')]
class ScopeIndexer extends TableIndexer
{
    protected function index_level(?Scope $parent): int {
        return $parent ? $parent->level + 1 : 0;
    }

    protected function index_id_path(?Scope $parent, int $id): string {
        return $parent ? "{$parent->id_path}/{$id}" : "{$id}";
    }

    protected function index_parent(?int $parent_id): Scope|Object_|null {
        return $parent_id
            ? query(Scope::class)
                ->equals('id', $parent_id)
                ->hydrate()
                ->first('level', 'id_path')
            : null;
    }
}