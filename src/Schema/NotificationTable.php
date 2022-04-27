<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Schema $schema
 * @property string $name #[Serialized]
 * @property ?string $rename #[Serialized]
 * @property string $table_name #[Serialized]
 * @property int $indexer_id #[Serialized]
 * @property string $suffix #[Serialized]
 * @property bool $cascade #[Serialized]
 * @property Table $table
 * @property Indexer $indexer
 *
 * @uses Serialized
 */
class NotificationTable extends Object_
{
    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_table_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_indexer_id(): string {
        throw new Required(__METHOD__);
    }

    protected function get_suffix(): string {
        throw new Required(__METHOD__);
    }

    protected function get_cascade(): bool {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        return "{$this->table_name}__{$this->indexer_id}__{$this->suffix}";
    }

    protected function get_rename(): ?string {
        return $this->table->rename
            ? "{$this->table->rename}__{$this->indexer_id}__{$this->suffix}"
            : null;
    }

    protected function get_table(): Table {
        return $this->schema->tables[$this->table_name];
    }

    protected function get_indexer(): Indexer {
        foreach ($this->schema->indexers as $indexer) {
            if ($indexer->id === $this->indexer_id) {
                return $indexer;
            }
        }

        throw new NotImplemented($this);
    }
}