<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use function Osm\__;
use function Osm\sort_by_dependency;

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
        $this->classes = Reflector::new(['schema' => $this])->getClasses();

        $this->classes = sort_by_dependency($this->classes, __("Classes"),
            fn($positions) =>
            fn(Class_ $a, Class_ $b) =>
                $positions[$a->name] <=> $positions[$b->name]
        );

        return $this->classes;
    }

    public function __wakeup(): void {
        foreach ($this->classes as $class) {
            $class->schema = $this;
        }
    }
}