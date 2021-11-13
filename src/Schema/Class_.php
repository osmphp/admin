<?php

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Schema $schema
 * @property string $name #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property Class_[] $types
 * @property CoreClass $reflection
 */
class Class_ extends Object_
{
    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): CoreClass {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->classes[$this->name];
    }

    protected function get_types(): array {
        return array_map(
            fn(string $className) => $this->schema->classes[$className],
            $this->reflection->types ?? []);
    }

    public function __wakeup(): void
    {
        foreach ($this->properties as $property) {
            $property->class = $this;
        }
    }
}