<?php

namespace Osm\Admin\Scopes\Indexers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Base\Attributes\Indexer\From;
use Osm\Admin\Base\Attributes\Indexer\To;
use Osm\Admin\Base\Attributes\Indexer\ThisSource;
use Osm\Admin\Queries\Query;
use Osm\Admin\Scopes\Scope;
use Osm\Admin\Scopes\Scopes;
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
#[To('scopes'), From('scopes', name: 'parent')]
class ScopeIndexer extends TableIndexer
{
    protected function index_level(?int $parent__level): int {
        return $parent__level ? $parent__level + 1 : 0;
    }

    protected function index_id_path(?string $parent__id_path, int $id): string {
        return $parent__id_path ? "{$parent__id_path}/{$id}" : "{$id}";
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function index(int $id = null, bool $incremental = true): void {
        if ($id) {
            $query = $this->query()->equals('id', $id);
            $data = $this->indexObject($query->first());
            $query->batchUpdate($data);

            return;
        }

        $this->db->transaction(function() use ($incremental) {
            $count = query(Scope::class)
                ->prepareSelect()
                ->max('level') + 1;

            for ($level = 0; $level < $count; $level++) {
                if ($incremental) {
                    foreach ($this->sources as $source) {
                        $this->indexLevel($level, $source);
                    }
                }
                else {
                    $this->indexLevel($level);
                }
            }
        });
    }

    protected function indexLevel(int $level, string $source = null): void {
        $query = $this->query($source)
            ->equals('parent.level', $level);

        $query->chunk(function (\stdClass $object) {
            $this->indexObject($object);
        });
    }

    public function update(callable $filter): void {
        $query = $this->query();
        $filter($query);

        $query->chunk(function (\stdClass $object) {
            $this->indexObject($object);
        });
    }

    protected function query(string $source = null): Scopes|Query
    {
        $query = query(Scope::class)
            ->select(...$this->depends_on);

        if ($source) {
            $query->raw(fn(Scopes $q) =>
                $this->changed($q, $source, "{$source}__changed"));
        }

        return $query;
    }

    protected function changed(Scopes $query, string $source, string $alias)
        : QueryBuilder
    {
        return $query->db_query->join("changed__{$this->index} AS {$alias}",
            "{$alias}.id", '=', "{$source}.id");
    }

    protected function indexObject(\stdClass $object): \stdClass
    {
        $id = $object->id;
        $data = new \stdClass();

        foreach ($this->properties as $property) {
            $arguments = [];

            foreach ($property->depends_on as $formula) {
                $identifiers = explode('.', $formula);
                $identifier = array_shift($identifiers);

                $argument = $object->$identifier ?? $data->$identifier ?? null;
                foreach ($identifiers as $identifier) {
                    if ($argument === null) {
                        break;
                    }

                    $argument = $argument->$identifier;
                }

                $arguments[] = $argument;
            }

            $data->{$property->name} =
                $this->{"index_{$property->name}"}(...$arguments);
        }

        return $data;
    }
}