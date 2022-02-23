<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
/**
 * @property Table $table
 * @property string[] $select_identifiers #[Serialized]
 * @property Column[] $selects #[Serialized]
 *
 * @uses Serialized
 */
class Grid extends Object_
{
    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_column_names(): array {
        throw new Required(__METHOD__);
    }

    protected function get_columns(): array {
        $columns = [];

        foreach ($this->select_identifiers as $columnName) {
            $columns[$columnName] = Column::new([
                'grid' => $this,
                'identifier' => $columnName,
            ]);
        }

        return $columns;
    }

    public function __wakeup(): void
    {
        foreach ($this->selects as $column) {
            $column->grid = $this;
        }
    }
}