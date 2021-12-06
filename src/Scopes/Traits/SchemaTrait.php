<?php

namespace Osm\Admin\Scopes\Traits;

use Osm\Admin\Schema\Schema;
use Osm\Admin\Scopes\ScopedTable;
use Osm\Core\Attributes\UseIn;

#[UseIn(Schema::class)]
trait SchemaTrait
{
    public function migrateScopeUp(int $scopeId, ?Schema $current = null): void {
        /* @var Schema|\Osm\Admin\Storages\Traits\SchemaTrait $this */

        foreach ($this->classes as $class) {
            if (!($scopedTable = $class->storage)) {
                continue;
            }

            if (!($scopedTable instanceof ScopedTable)) {
                continue;
            }

            $currentStorage = $current->classes[$class->name]->storage ?? null;
            if ($currentStorage) {
                $scopedTable->alterScope($scopeId, $currentStorage);
            }
            else {
                $scopedTable->createScope($scopeId);
            }
        }
    }

    public function migrateScopeDown(int $scopeId, ?Schema $current = null): void {
        /* @var Schema|\Osm\Admin\Storages\Traits\SchemaTrait $this */

        foreach (array_reverse($this->classes) as $class) {
            if (!($scopedTable = $class->storage)) {
                continue;
            }

            if (!($scopedTable instanceof ScopedTable)) {
                continue;
            }

            $scopedTable->dropScope($scopeId);
        }
    }
}