<?php

namespace Osm\Admin\Scopes;

use Osm\Admin\Tables\TableQuery;

class Scopes extends TableQuery
{
    protected function insertCommitted(\stdClass $data): void
    {
        $this->class->schema->migrateScopeUp($data->id);
        parent::insertCommitted($data);
    }
}