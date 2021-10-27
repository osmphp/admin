<?php

namespace Osm\Data\Queries;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Data\Queries\Attributes\Of;
use Osm\Data\Schema\Class_;

/**
 * @property string[] $select
 * @property ?int $limit
 *
 * @property string $object_class_name
 * @property Class_ $object_class
 */
class Query extends Object_
{
    public const DEFAULT_LIMIT = 10;

    public function select(array $select): static {
        foreach ($select as $key => $value) {
            if (!is_int($key)) {
                $this->selectPropertyWithCallback($key, $value);
            }
            elseif ($value = '~') {
                $this->selectAllProperties();
            }
            elseif ($value = '~~') {
                $this->selectAllPropertiesRecursively();
            }
            else {
                $this->selectProperty($value);
            }
        }

        return $this;
    }

    protected function selectProperty(string $property): void
    {
        if (($pos = strpos($property, '.')) !== false) {
            throw new NotImplemented($this);
        }

        $this->select[$property] = true;
    }

    protected function selectPropertyWithCallback(string $property,
        callable $callback): void
    {
        throw new NotImplemented($this);
    }

    protected function selectAllProperties(): void
    {
        foreach ($this->object_class->properties as $property) {
            throw new NotImplemented($this);

            if ($property->primitive) {
                $this->selectProperty($property->name);
            }
        }
    }

    protected function selectAllPropertiesRecursively(): void
    {
        throw new NotImplemented($this);
    }

    public function get(array $select = null): Result {
        if (is_array($select)) {
            $this->select($select);
        }

        if (empty($this->select)) {
            $this->select(['~']);
        }

        return $this->run();
    }

    public function first(array $select = null): \stdClass|Object_|null {
    }

    protected function get_limit(): ?int {
        return static::DEFAULT_LIMIT;
    }

    protected function run(): Result {
        throw new NotImplemented($this);
    }

    protected function get_object_class_name(): string {
        /* @var Of $of */
        return ($of = $this->__class->attributes[Of::class] ?? null)
            ? $of->class_name
            : throw new Required(__METHOD__);
    }

    protected function get_object_class(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$this->object_class_name]
            ?? throw new Required(__METHOD__);
    }
}