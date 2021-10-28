<?php

namespace Osm\Data\Migrations;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Schema\Class_;
use Osm\Data\Schema\Diff;
use Osm\Data\Scopes\Scope;

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

        //$this->sort();

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

        if ($this->scope && !$class->scoped ||
            !$this->scope && $class->scoped)
        {
            return;
        }

        $this->migrations[] = Migration\Table\Create::new([
            'class' => $class,
            'scope' => $this->scope,
        ]);
    }

    protected function dropClass(Class_ $class): void
    {
        throw new NotImplemented($this);
    }


}