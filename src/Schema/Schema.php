<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_[] $classes #[Serialized]
 */
class Schema extends Object_
{
    /**
     * @var int
     */
    #[Serialized]
    public int $version = 1;

    protected function get_classes(): array {
        return Reflector::new(['schema' => $this])->getClasses();
    }

    public function __wakeup(): void {
        foreach ($this->classes as $class) {
            $class->schema = $this;
        }
    }
}