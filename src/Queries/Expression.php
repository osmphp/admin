<?php

namespace Osm\Admin\Queries;

use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;

/**
 * @property Query $query
 * @property string $text
 */
class Expression extends Object_
{
    use SubTypes;

    public function select(): void {
        $this->query->selects[$this->text] = Select::new([
            'query' => $this->query,
            'expression' => $this,
        ]);
    }
}