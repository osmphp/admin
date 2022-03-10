<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Schema\Hints\IndexerStatus;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
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
 *
 * @uses Serialized
 */
class Indexer extends Object_
{
    public const PARTIAL = 'partial';
    public const FULL = 'full';

    /**
     * @param IndexerStatus[] $status
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
     * @param IndexerStatus[] $status
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
}