<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_[] $classes #[Serialized]
 * @property Class_\Table[] $tables
 */
class Schema extends Object_
{
    protected function get_classes(): array {
        throw new NotImplemented($this);
    }

    protected function get_tables(): array {
        throw new NotImplemented($this);
    }

    public function __wakeup(): void {
        foreach ($this->classes as $class) {
            $class->schema = $this;
        }
    }
}