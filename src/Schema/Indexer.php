<?php

namespace Osm\Admin\Schema;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Hints\Indexer\Status;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Db\Db;

/**
 * @property Schema $schema
 * @property string $table_name #[Serialized]
 * @property string $short_name #[Serialized]
 * @property int $id #[Serialized]
 * @property string $name #[Serialized]
 * @property string[] $after #[Serialized]
 * @property string[] $after_regexes #[Serialized]
 * @property Table $table
 * @property Db $db
 * @property array $listens_to #[Serialized]
 *
 * @uses Serialized
 */
class Indexer extends Object_
{
    public const PARTIAL = 'partial';
    public const FULL = 'full';

    /**
     * @param Status[] $status
     * @param string $mode
     * @return ?string
     */
    public function requiresReindex(array $status, string $mode): ?string {
        if ($mode == static::FULL) {
            return static::FULL;
        }

        if ($status[$this->id]->requires_full_reindex) {
            return static::FULL;
        }

        return $status[$this->id]->requires_partial_reindex
            ? static::PARTIAL
            : null;
    }

    /**
     * @param string $mode
     */
    public function index(string $mode): void {
        throw new NotImplemented($this);
    }

    /**
     * @param Status[] $status
     */
    public function markAsIndexed(array &$status): void {
        $status[$this->id]->requires_partial_reindex = false;
        $status[$this->id]->requires_full_reindex = false;

        $this->db->table('indexers')
            ->where('id', $this->id)
            ->update([
                'requires_partial_reindex' => false,
                'requires_full_reindex' => false,
            ]);
    }

    public function markAsRequiringPartialReindex(): void {
        $this->db->table('indexers')
            ->where('id', $this->id)
            ->update([
                'requires_partial_reindex' => true,
            ]);
    }

    public function createNotificationTables(Table $source): void  {
        $listensTo = $this->listens_to[$source->name];

        if ($inserted = $listensTo[Query::INSERTED] ?? null) {
            $this->createNotificationTable($source, $inserted, cascade: true);
        }

        if (($updated = $listensTo[Query::UPDATED] ?? null) &&
            $updated != $inserted)
        {
            $this->createNotificationTable($source, $updated, cascade: true);
        }

        if ($deleted = $listensTo[Query::DELETED] ?? null) {
            $this->createNotificationTable($source, $deleted, cascade: true);
        }
    }

    protected function createNotificationTable(Table $source, string $suffix,
        bool $cascade = false): void
    {
        $this->db->create($this->getNotificationTableName($source, $suffix),
            function(Blueprint $table) use ($source, $cascade) {
                $table->integer('id')->unsigned()->unique();

                if ($cascade) {
                    $table->foreign('id')
                        ->references('id')
                        ->on($source->table_name)
                        ->onDelete('cascade');
                }
            }
        );
    }

    protected function get_listens_to(): array {
        throw new NotImplemented($this);
    }

    public function notify(Query $query, string $event, array $data): void {
        $listensTo = $this->listens_to[$query->table->name];

        if (!($suffix = $listensTo[$event] ?? null)) {
            // do nothing if the indexer is not listening to $event
            return;
        }

        $tableName = $this->getNotificationTableName($query->table, $suffix);

        if ($event == Query::INSERTED) {
            $this->db->table($tableName)->insert([
                'id' => $data['id'],
            ]);
        }
        else {
            $query->clone()->select('id')->into($tableName);
        }

        $this->markAsRequiringPartialReindex();
    }

    protected function get_after_regexes(): array {
        throw new NotImplemented($this);
    }

    protected function get_after(): array {
        $after = [];

        foreach ($this->after_regexes as $regex) {
            foreach ($this->schema->indexers as $indexer) {
                if ($indexer === $this) {
                    continue;
                }

                if (preg_match($regex, $indexer->name)) {
                    $after[$indexer->name] = true;
                }
            }
        }

        return array_keys($after);
    }

    protected function get_table_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_short_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        return "{$this->table_name}__{$this->short_name}";
    }

    protected function get_table(): Table {
        return $this->schema->tables[$this->table_name];
    }

    protected function get_id(): int {
        $id = $this->db->table('indexers')
            ->where('name', $this->name)
            ->value('id');

        return $id ?? $this->db->table('indexers')->insertGetId([
            'name' => $this->name,
        ]);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function getNotificationTableName(Table $table, string $suffix)
        : string
    {
        return "zi{$this->id}__{$table->table_name}__{$suffix}";
    }
}