<?php

namespace Osm\Data\Migrations;

use Illuminate\Database\Schema\Blueprint as TableBlueprint;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Schema\Class_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Schema\Diff;
use Osm\Data\Schema\Schema;
use Osm\Data\Scopes\Scope;
use Osm\Data\Tables\Query;
use Osm\Framework\Db\Db;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property Schema $new
 * @property Schema $old
 * @property OutputInterface $output
 * @property Diff $diff
 * @property Migration[] $global_migrations
 *
 * @property Db $db
 */
class Migrator extends Object_
{
    public function migrate(): void {
        global $osm_app; /* @var App $osm_app */

        $this->migrateFromEarlierSchemaVersions();
        $this->migrateScopeFromEarlierSchemaVersions();

        $this->migrateNonScopedClasses();
        $this->migrateScopedClasses($osm_app->root_scope);
    }

    protected function migrateNonScopedClasses(): void
    {
        $migrations = Planner::new([
            'diff' => $this->diff,
        ])->plan();

        foreach ($migrations as $migration) {
            $migration->run();
        }
    }

    protected function migrateScopedClasses(Scope $scope): void
    {
        $migrations = Planner::new([
            'diff' => $this->diff,
            'scope' => $scope,
        ])->plan();

        foreach ($migrations as $migration) {
            $migration->run();
        }
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_diff(): Diff {
        return Diff::new(['old' => $this->old, 'new' => $this->new]);
    }

    protected function migrateFromEarlierSchemaVersions(): void {
        if (!$this->old) {
            return;
        }

        for ($version = $this->old->version;
            $version < $this->new->version; $version++)
        {
            $new = "{$this->__class->name}\\FromEarlierSchema\\" .
                "Version{$version}::new";

            $new([
                'schema' => $this->old,
                'output' => $this->output,
            ])->run();
        }
    }

    protected function migrateScopeFromEarlierSchemaVersions(
        int $scopeId = null): void
    {
        if (!$this->old) {
            return;
        }

        for ($version = $this->old->version;
            $version < $this->new->version; $version++)
        {
            $new = "{$this->__class->name}\\FromEarlierScopeSchema\\" .
                "Version{$version}::new";

            $new([
                'schema' => $this->old,
                'scope_id' => $scopeId,
                'output' => $this->output,
            ])->run();
        }
    }

    protected function get_output(): OutputInterface {
        return new BufferedOutput();
    }
}