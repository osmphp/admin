<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Indexing\Index;
use Osm\Admin\Storages\Storage;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Schema\Class_;

/**
 * @property Storage $storage
 * @property Class_ $class
 * @property string[]|null $select
 * @property ?int $limit
 * @property ?Index $index
 */
class Query extends Object_
{
    public const DEFAULT_LIMIT = 10;

    protected function get_storage(): Storage {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        return $this->storage->class;
    }

    public function select(array $select): static {
        foreach ($select as $key => $value) {
            if (!is_int($key)) {
                $this->selectPropertyWithCallback($key, $value);
            }
            elseif ($value = '*') {
                $this->selectAllProperties();
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
        foreach ($this->class->properties as $property) {
            $this->selectProperty($property->name);
        }
    }

    public function get(array $select = null): Result {
        if (is_array($select)) {
            $this->select($select);
        }

        if (empty($this->select)) {
            $this->select(['*']);
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

    protected function get_index(): ?Index {
        return $this->storage->targeted_by[$this->data->type ?? ''] ?? null;
    }
}