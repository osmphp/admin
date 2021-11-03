<?php

namespace Osm\Admin\Migrations;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Scopes\Scope;
use Osm\Framework\Db\Db;

/**
 * @property Planner $planner
 * @property Scope $scope null when creating global tables
 * @property Db $db
 * @property ?int $priority
 * @property string $name
 * @property string[] $after
 */
class Migration extends Object_
{
    public function run(): void {
        throw new NotImplemented($this);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_after(): array {
        return [];
    }
}