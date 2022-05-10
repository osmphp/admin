<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property Property $property
 * @property string $mode
 * @property ?Blueprint $table
 */
class Migration extends Object_
{
    protected bool $run = false;

    protected function get_property(): Property {
        throw new Required(__METHOD__);
    }

    protected function get_mode(): string {
        throw new Required(__METHOD__);
    }

    public function migrate(): bool {
        throw new NotImplemented($this);
    }
}