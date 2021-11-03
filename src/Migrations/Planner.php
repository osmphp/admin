<?php

namespace Osm\Admin\Migrations;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Scopes\Scope;
use function Osm\__;
use function Osm\sort_by_dependency;

/**
 * @property Diff $diff
 * @property Scope $scope
 *
 * @property Migration[] $migrations
 */
class Planner extends Object_
{
    public function plan(): array {
        $this->migrations = [];

        foreach ($this->diff->classes as $class) {
            $this->planClass($class);
        }

        $this->sort();

        return $this->migrations;
    }

    protected function planClass(Diff\Class_ $class): void  {
        if (!$class->old) {
            $this->createClass($class->new);
        }
        elseif ($class->new) {
            $this->dropClass($class->old);
        }
        else {
            throw new NotImplemented($this);
        }
    }


    protected function createClass(Class_ $class): void
    {
        if (!$class->table) {
            return;
        }

        // $this->scope is null when creating global tables
        // $class->scoped is true if the table is scope-specific
        if ($this->scope && !$class->scoped) {
            return;
        }

        $this->register(Migration\Table\Create::new([
            'planner' => $this,
            'class' => $class,
            'scope' => $this->scope,
        ]));
    }

    protected function dropClass(Class_ $class): void {
        throw new NotImplemented($this);
    }

    protected function sort(): void {
        $this->migrations = sort_by_dependency($this->migrations,
            __("Migrations"),
            fn($positions) =>
                fn(Migration $a, Migration $b) =>
                    $this->compare($a, $b, $positions)
        );
    }

    protected function register(Migration $migration): void
    {
        $this->migrations[$migration->name] = $migration;
    }

    protected function compare(Migration $a, Migration $b, array $positions): int
    {
        return ($a->priority ?? PHP_INT_MAX) !== ($b->priority ?? PHP_INT_MAX)
            ? ($a->priority ?? PHP_INT_MAX) <=> ($b->priority ?? PHP_INT_MAX)
            : $positions[$a->name] <=> $positions[$b->name];
    }


}