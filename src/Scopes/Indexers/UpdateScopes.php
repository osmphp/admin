<?php

namespace Osm\Admin\Scopes\Indexers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Base\Attributes\On;
use Osm\Admin\Indexing\Event;
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
use Osm\Core\Attributes\Serialized;

/**
 * @property Db $db
 * @property bool $updates_data #[Serialized]
 */
#[On\Saving('scopes'), On\Saved('scopes', alias: 'parent')]
class UpdateScopes extends TableIndexer
{
    protected function index_level(?int $parent__level): int {
        return $parent__level !== null ? $parent__level + 1 : 0;
    }

    protected function index_id_path(?string $parent__id_path, int $id): string {
        return $parent__id_path !== null ? "{$parent__id_path}/{$id}" : "{$id}";
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function index(int $id = null, Event $event = null): void {
        if ($id) {
            $query = $this->query()->equals('id', $id);
            $data = $this->indexObject($query, $query->first());
            $query->doUpdate($data);

            return;
        }

        $count = query(Scope::class)
            ->prepareSelect()
            ->max('level') + 1;

        for ($level = 0; $level < $count; $level++) {
            $this->indexLevel($level, $event);
        }
    }

    protected function indexLevel(int $level, Event $event = null): void {
        $query = $this->query($event)
            ->equals('parent.level', $level)
            ->orderBy('id');

        $query->chunk(function (\stdClass $object) use ($query) {
            $data = $this->indexObject($query, $object);
            query(Scope::class)
                ->equals('id', $object->id)
                ->update($data);
        });
    }

    protected function query(Event $event = null): Scopes|Query
    {
        $query = query(Scope::class)
            ->select(...$this->depends_on);

        if ($this->updates_data) {
            $query->selectData($this->properties);
        }

        if ($event) {
            $query->raw(fn(Scopes $q) =>
                $q->db_query->join("{$event->notification_table} AS " .
                    "{$event->alias}__notification",
                    "{$event->alias}__notification.id", '=',
                    "{$event->alias}.id")
            );
        }

        return $query;
    }

    protected function indexObject(TableQuery $query, \stdClass $object):
        \stdClass
    {
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

        if ($this->updates_data) {
            $data = $query->mergeData($data, $object);
        }

        return $data;
    }

    protected function get_updates_data(): bool {
        return query(Scope::class)->updatesData($this->properties);
    }
}