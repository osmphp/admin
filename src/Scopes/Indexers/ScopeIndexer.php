<?php

namespace Osm\Admin\Scopes\Indexers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Base\Attributes\Indexer\Source;
use Osm\Admin\Base\Attributes\Indexer\Target;
use Osm\Admin\Scopes\Scope;
use Osm\Admin\Tables\TableIndexer;
use Osm\Admin\Tables\TableQuery;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Framework\Db\Db;
use function Osm\query;

/**
 * @property Db $db
 */
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

    public function run(): void {
        $this->db->transaction(function() {
            $count = query(Scope::class)
                ->prepareSelect()
                ->max('level') + 1;

            for ($level = 0; $level < $count; $level++) {
                $this->runLevel($level);
            }
        });
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function runLevel(int $level): void {
        $scopes = query(Scope::class)
            ->equals('parent.level', $level)
            ->changed('parent', $this->id)
            ->select(...$this->index->source_properties);

        $scopes->chunk(function (\stdClass $item) {
            throw new NotImplemented($this);
        });
    }
}