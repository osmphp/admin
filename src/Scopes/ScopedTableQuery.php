<?php

namespace Osm\Admin\Scopes;

use Osm\Core\App;
use Osm\Admin\Tables\TableQuery;

/**
 * @property Scope $scope
 * @property string $global_name
 */
class ScopedTableQuery extends TableQuery
{
    protected function get_scope(): Scope {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->scope ?? $osm_app->root_scope;
    }

    protected function get_global_name(): string {
        return parent::get_name();
    }

    protected function get_name(): string
    {
        return $this->scope->prefix . parent::get_name();
    }

    protected function doInsert(array $values): ?int
    {
        $id = $this->db->table($this->global_name)->insertGetId([
            'scope_id' => $this->scope->id,
        ]);

        $this->db->table($this->name)->insert(
            array_merge(['id' => $id], $values));

        return $id;
    }
}