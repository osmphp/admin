<?php

namespace Osm\Admin\Scopes;

use Osm\Admin\Tables\TableQuery;

class Scopes extends TableQuery
{
    protected function insertCommitted(): void
    {
        $this->class->schema->migrateScopeUp($this->data->id);
        parent::insertCommitted();
    }
}