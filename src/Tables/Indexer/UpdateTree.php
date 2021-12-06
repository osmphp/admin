<?php

namespace Osm\Admin\Tables\Indexer;

use Osm\Admin\Indexing\Event;
use Osm\Admin\Indexing\Indexer;

use Osm\Admin\Queries\Query;
use Osm\Admin\Tables\Table;
use Osm\Admin\Tables\TableQuery;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Db\Db;
use function Osm\query;

/**
 * @property Db $db
 * @property bool $updates_data #[Serialized]
 * @property string $class_name #[Serialized]
 */
class UpdateTree extends Indexer
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_class_name(): string {
        global $osm_app; /* @var App $osm_app */

        if (!($name = $this->events['']->table ?? null)) {
            throw new NotImplemented($this);
        }

        foreach ($osm_app->schema->classes as $class) {
            if (!$class->storage) {
                continue;
            }

            if (!($class->storage instanceof Table)) {
                continue;
            }

            if ($class->storage->name == $name) {
                return $class->name;
            }
        }

        throw new NotImplemented($this);
    }

    protected function get_updates_data(): bool {
        return query($this->class_name)->updatesData($this->properties);
    }

    public function index(Event $event = null): void {
        if ($event && $event->record_id) {
            $query = $this->query()->equals('id', $event->record_id);
            $data = $this->indexObject($query, $query->first());
            $query->doUpdate($data);

            return;
        }

        $count = query($this->class_name)
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
            query($this->class_name)
                ->equals('id', $object->id)
                ->update($data);
        });
    }

    protected function query(Event $event = null): TableQuery|Query
    {
        $query = query($this->class_name)
            ->select(...$this->depends_on);

        if ($this->updates_data) {
            $query->selectData($this->properties);
        }

        if ($event) {
            $query->raw(fn(TableQuery $q) =>
            $q->db_query->join("{$event->notification_table} AS " .
                "{$event->alias}__notification",
                "{$event->alias}__notification.id", '=',
                "{$event->alias}.id")
            );
        }

        return $query;
    }

    protected function indexObject(TableQuery $query, \stdClass $object)
        : \stdClass
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
}