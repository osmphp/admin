<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Core\Attributes\Type;
use Osm\Admin\Schema\Property\Int_ as IntPropertyObject;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property \stdClass|IntPropertyObject|null $old
 * @property IntPropertyObject $new
 */
#[Type('int')]
class Int_ extends Scalar {
    public function diff(): void {
        $this->explicit();
        $this->type();
        $this->nullable();
        $this->size();
        $this->unsigned();
        $this->autoIncrement();
    }

    protected function define(Blueprint $table): ColumnDefinition {
        return $table->integer($this->new->name);
    }

    protected function size(): void {
        $this->attribute('size', function() {
        });
    }

    protected function unsigned(): void {
        $this->attribute('unsigned', function() {
        });
    }

    protected function autoIncrement(): void {
        $this->attribute('auto_increment', function() {
        });
    }
}