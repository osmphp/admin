<?php

namespace Osm\Admin\Scopes;

use Osm\Admin\Tables\TableQuery;

class Scopes extends TableQuery
{
    protected function inserted(int $id): void
    {
        $this->class->schema->migrateScopeUp($id);
    }
}