<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Indexer;
use Osm\Admin\Schema\Table as TableObject;
use Osm\Admin\Schema\NotificationTable as NotificationTableObject;
use Osm\Core\Exceptions\Required;

/**
 * @property Schema $schema
 * @property \stdClass|NotificationTableObject|null $old
 * @property NotificationTableObject $new
 * @property TableObject $source
 * @property Indexer $indexer
 * @property string $suffix
 * @property bool $cascade
 * @property bool $changed
 * @property string $old_db_table_name
 * @property string $new_db_table_name
 */
class NotificationTable extends Diff
{
    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_new(): NotificationTableObject {
        throw new Required(__METHOD__);
    }

    protected function get_source(): TableObject {
        return $this->new->table;
    }

    protected function get_indexer(): Indexer {
        return $this->new->indexer;
    }

    protected function get_suffix(): string {
        return $this->new->suffix;
    }

    protected function get_cascade(): bool {
        return $this->new->cascade;
    }

    public function migrate(): void {
        if (!$this->changed) {
            return;
        }

        if ($this->old) {
            $this->drop();
        }

        $this->create();
    }

    protected function create(): void {
        $this->db->create($this->new_db_table_name, function(Blueprint $table) {
            $table->integer('id')->unsigned()->unique();

            if ($this->cascade) {
                $table->foreign('id')
                    ->references('id')
                    ->on($this->source->table_name)
                    ->onDelete('cascade');
            }
        });
    }

    public function diff(): void {
//        throw new NotImplemented($this);
    }

    protected function get_changed(): bool {
        if (!$this->old) {
            return true;
        }

        if ($this->old->table_name !== $this->new->table_name) {
            return true;
        }

        if ($this->old->indexer_id !== $this->new->indexer_id) {
            return true;
        }

        if ($this->old->suffix !== $this->new->suffix) {
            return true;
        }

        if ($this->old->cascade !== $this->new->cascade) {
            return true;
        }

        return false;
    }

    protected function get_old_db_table_name(): string {
        return "zi{$this->old->indexer_id}__{$this->old->table->table_name}" .
            "__{$this->old->suffix}";
    }

    protected function get_new_db_table_name(): string {
        return $this->indexer->getNotificationTableName($this->source,
            $this->suffix);
    }

    protected function drop(): void {
        $this->db->drop($this->old_db_table_name);
    }
}