<?php

namespace Osm\Admin\Storages\Traits;

use Osm\Admin\Schema\Property;
use Osm\Admin\Tables\Interfaces\HasColumns;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property bool $stored #[Serialized]
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_stored(): bool {
        /* @var Property|static $this */
        if (!$this->class->storage) {
            return true;
        }

        $type = $this->reflection->type;
        if (!($class = $this->class->schema->classes[$type] ?? null)) {
            return true;
        }

        return $class->storage == null;
    }
}