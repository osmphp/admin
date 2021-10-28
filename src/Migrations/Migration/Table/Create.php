<?php

namespace Osm\Data\Migrations\Migration\Table;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Migrations\Migration\Table;
use Osm\Data\Schema\Class_;
use Osm\Data\Schema\Property;

/**
 * @property Class_ $class
 */
class Create extends Table
{
    public function run(): void
    {
        $this->db->create($this->class->table, function(Blueprint $table) {
            $this->createColumns($table, $this->class);
            $table->json('data')->nullable();
        });
    }

    protected function createColumns(Blueprint $table, Class_ $class,
        string $prefix = ''): void
    {
        foreach ($class->properties as $property) {
            if ($property->column) {
                $this->createColumn($table, $property, $prefix);
                continue;
            }

            if (isset($class->schema->classes[$property->type])) {
                throw new NotImplemented($this);
            }
        }
    }
}