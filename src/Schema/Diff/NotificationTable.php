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
        $this->create();
    }

    protected function create(): void {
        $table = $this->indexer->getNotificationTableName($this->source,
            $this->suffix);

        $this->db->create($table, function(Blueprint $table) {
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
}