<?php

namespace Osm\Data\Queries;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Schema\Class_;

/**
 * @property string[] $select
 * @property ?int $limit
 *
 * @property Class_ $class
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
                $this->selectPrimitiveProperties();
            }
            elseif ($value = '~~') {
                $this->selectPrimitivePropertiesRecursively();
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

    protected function selectPrimitiveProperties(): void
    {
        foreach ($this->class->properties as $property) {
            if ($property->primitive) {
                $this->selectProperty($property->name);
            }
        }
    }

    protected function selectPrimitivePropertiesRecursively(): void
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

    protected function get_class(): Class_ {
        throw new NotImplemented($this);
    }
}