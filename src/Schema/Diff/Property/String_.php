<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Core\Attributes\Type;

use Osm\Admin\Schema\Property\String_ as StringPropertyObject;

/**
 * @property \stdClass|StringPropertyObject|null $old
 * @property StringPropertyObject $new
 */
#[Type('string')]
class String_ extends Scalar {
    public function diff(): void {
        $this->explicit();
        $this->type();
        $this->nullable();
        $this->size();
        $this->length();
    }

    protected function define(Blueprint $table): ColumnDefinition {
        return $table->text($this->new->name);
    }

    protected function size(): void {
        $this->attribute('size', function() {
        });
    }

    protected function length(): void {
        $this->attribute('max_length', function() {
        });
    }
}