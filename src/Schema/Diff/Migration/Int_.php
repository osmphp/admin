<?php

namespace Osm\Admin\Schema\Diff\Migration;

use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Schema\Diff\Migration;
use Osm\Admin\Schema\Diff\Property;
use Osm\Core\Exceptions\NotImplemented;
use function Osm\__;

class Int_ extends Migration
{
    public function migrate(): void {
        $this->init();
        $this->explicit();
        $this->type();
        $this->nullable();
        $this->size();
        $this->unsigned();
        $this->autoIncrement();
    }

    protected function column(): ColumnDefinition {
        return $this->table->integer($this->property->new->name);
    }

    protected function size(): void {
        throw new NotImplemented($this);
    }

    protected function unsigned(): void {
        throw new NotImplemented($this);
    }

    protected function autoIncrement(): void {
        throw new NotImplemented($this);
    }
}