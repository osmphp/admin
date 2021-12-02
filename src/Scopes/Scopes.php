<?php

namespace Osm\Admin\Scopes;

use Osm\Admin\Tables\TableQuery;

class Scopes extends TableQuery
{
    protected function insertCommitted(int $id, \stdClass $data): void
    {
        $this->class->schema->migrateScopeUp($id);
        parent::insertCommitted($id, $data);
    }
}