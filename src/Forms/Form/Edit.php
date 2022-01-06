<?php

namespace Osm\Admin\Forms\Form;

use Osm\Admin\Forms\FormData;
use Osm\Admin\Queries\Query;
use Osm\Admin\Tables\TableQuery;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property TableQuery $query
 */
#[Type('edit')]
class Edit //extends Form
{
    protected function get_query(): TableQuery {
        $this->query = $this->storage->query();
        $this->parseFilters();

        return $this->query;
    }

    protected function get_count(): int {
        return (clone $this->query)->count();
    }
}