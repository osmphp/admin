<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Table $table
 * @property string $name #[Serialized]
 * @property Property $property
 */
class Column extends Object_
{
    use SubTypes;

    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_property(): Property {
        return $this->table->class->properties[$this->name];
    }

    public function create(Blueprint $table): void {
        throw new NotImplemented($this);
    }

    public function drop(Blueprint $table): void {
        throw new NotImplemented($this);
    }
}