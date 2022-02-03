<?php

namespace Osm\Admin\Schema\Class_;

use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property string $table_name #[Serialized]
 * @property string[] $column_names #[Serialized]
 * @property Property[] $columns
 */
class Table extends Class_
{
    protected function get_table_name(): string {
        throw new NotImplemented($this);
    }

    protected function get_column_names(): array {
        throw new NotImplemented($this);
    }

    protected function get_columns(): array {
        throw new NotImplemented($this);
    }


}