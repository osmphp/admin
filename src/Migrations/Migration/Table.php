<?php

namespace Osm\Admin\Migrations\Migration;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Migrations\Migration;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Tables\Column;

class Table extends Migration
{
    protected function references(Class_ $class1, Class_ $class2): bool
    {
        foreach ($class1->properties as $property) {
            /* @var Column\Int_ $column */
            if (!($column = $property->column)) {
                continue;
            }

            if ($column->references_table &&
                $column->references_table === $class2->table)
            {
                return true;
            }
        }

        return false;
    }
}