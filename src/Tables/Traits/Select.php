<?php

namespace Osm\Admin\Tables\Traits;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Queries\Query;
use Osm\Admin\Queries\Result;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Tables\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use function Osm\hydrate;
use function Osm\merge;

/**
 * @property QueryBuilder $raw (prepared in class)
 * @property string[] $select (prepared in class)
 * @property Class_ $class (prepared in class)
 * @property Table $storage (prepared in class)
 * @property bool $hydrate (prepared in class)
 */
trait Select
{
    protected function run(): Result
    {
        $this->applySelect();

        return Result::new([
            'items' => $this->raw->get([])
                ->map(fn(\stdClass $item) => $this->load($item))
                ->toArray(),
        ]);
    }

    public function chunk(callable $callback,
        int $chunkSize = Query::DEFAULT_CHUNK_SIZE): void
    {
        if (empty($this->select)) {
            $this->select(['*']);
        }

        $this->raw
            ->orderBy('id')
            ->chunk($chunkSize, function($items) use ($callback) {
                foreach ($items as $item) {
                    $callback($this->load($item));
                }
            });
    }

    protected function applySelect(): void
    {
        if (empty($this->select)) {
            throw new NotImplemented($this);
        }

        foreach (array_keys($this->select) as $propertyName) {
            if (!($property = $this->class->properties[$propertyName]
                ?? null))
            {
                throw new NotImplemented($this);
            }

            if (isset($this->storage->columns[$propertyName])) {
                $this->raw->addSelect($property->name);
            }
            elseif (!in_array('data', $this->raw->columns)) {
                $this->raw->addSelect('data');
            }
        }
    }

    protected function load(\stdClass $item): \stdClass|Object_
    {
        if (isset($item->data)) {
            $item = merge($item, json_decode($item->data));
        }

        foreach ($item as $property => $value) {
            if (!isset($this->select[$property])) {
                unset($item->$property);
            }
        }

        return $this->hydrate
            ? hydrate($this->class->name, $item)
            : $item;
    }
}