<?php

namespace Osm\Admin\Schema;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

/**
 * @property Schema $old
 * @property Schema $new
 *
 * @property Diff\Class_[] $classes
 */
class Diff extends Object_
{
    protected function get_classes(): array {
        $classDiffs = [];

        if ($this->old) {
            // actual comparison will come later
            throw new NotImplemented($this);
        }

        foreach ($this->new->classes as $class) {
            $classDiffs[$class->name] = Diff\Class_::new([
                'new' => $class,
            ]);
        }

        return $classDiffs;
    }
}