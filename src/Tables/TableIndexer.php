<?php

namespace Osm\Admin\Tables;

use Osm\Admin\Indexing\Indexer;
use Osm\Admin\Queries\Query;
use Osm\Core\Exceptions\NotImplemented;

class TableIndexer extends Indexer
{
    /**
     * @param TableQuery $query
     */
    public function inserting(Query $query, \stdClass $data): void
    {
        foreach ($this->index->properties as $property) {
            if (property_exists($data, $property->name)) {
                continue;
            }

            if (in_array('id', $property->parameters)) {
                continue;
            }

            $values = [];

            foreach ($property->parameters as $parameter) {
                $values[] = $data->{$parameter} ?? null;
            }

            $data->{$property->name} =
                $this->{"index_{$property->name}"}(...$values);
        }
    }

    /**
     * @param TableQuery $query
     */
    public function inserted(Query $query, \stdClass $data, &$modified): void
    {
        foreach ($this->index->properties as $property) {
            if (property_exists($data, $property->name)) {
                continue;
            }

            $values = [];

            foreach ($property->parameters as $parameter) {
                $values[] = $data->{$parameter} ?? null;
            }

            $data->{$property->name} =
                $this->{"index_{$property->name}"}(...$values);
            $modified[$property->name] = true;
        }
    }

    /**
     * @param TableQuery $query
     */
    public function updating(Query $query, \stdClass $data, array &$modified): void
    {
        foreach ($this->index->properties as $property) {
            $values = [];

            foreach ($property->parameters as $parameter) {
                $values[] = $data->{$parameter} ?? null;
            }

            $data->{$property->name} =
                $this->{"index_{$property->name}"}(...$values);
            $modified[$property->name] = true;
        }
    }
}