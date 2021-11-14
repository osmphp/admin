<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Admin\Schema\Class_;
use Osm\Admin\Tables\Column;
use Osm\Admin\Tables\Interfaces\HasColumns;
use Osm\Admin\Tables\Table;
use Osm\Core\Attributes\Type;
use Osm\Core\Attributes\UseIn;

#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function around_get_after(callable $proceed): array {
        /* @var Class_|static $this */
        $after = $proceed();

        if (isset($this->reflection->attributes[Type::class])) {
            return $after;
        }

        if (!$this->storage) {
            return $after;
        }

        if (!($this->storage instanceof HasColumns)) {
            return $after;
        }

        foreach ($this->storage->columns as $column) {
            /* @var Column|ForeignKey $column */
            if (!($tableName = $column->references_table)) {
                continue;
            }

            if ($tableName === $this->storage->name) {
                continue;
            }

            foreach ($this->schema->classes as $class) {
                if (!$class->storage) {
                    continue;
                }

                if (!($class->storage instanceof HasColumns)) {
                    continue;
                }

                if ($class->storage->name === $tableName) {
                    $after[] = $class->name;
                    break;
                }
            }
        }

        return $after;
    }
}