<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
/**
 * @property Table $table
 * @property string[] $column_names #[Serialized]
 * @property Column[] $columns #[Serialized]
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

        foreach ($this->column_names as $columnName) {
            $columns[$columnName] = Column::new([
                'grid' => $this,
                'property_name' => $columnName,
            ]);
        }

        return $columns;
    }

    public function __wakeup(): void
    {
        foreach ($this->columns as $column) {
            $column->grid = $this;
        }
    }
}