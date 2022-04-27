<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Property\String_ as StringPropertyObject;
use Osm\Core\Attributes\Type;

#[Type('string')]
class String_ extends Scalar
{
    public function create(Blueprint $table): void {
        if ($this->new->max_length &&
            $this->new->max_length <= StringPropertyObject::VARCHAR_LENGTH)
        {
            $column = $table->string($this->new->name, $this->new->max_length);
        }
        else {
            $column = match ($this->new->size) {
                PropertyObject::TINY => $table->tinyText($this->new->name),
                PropertyObject::SMALL => $table->text($this->new->name),
                PropertyObject::MEDIUM => $table->mediumText($this->new->name),
                PropertyObject::LONG => $table->longText($this->new->name),
            };
        }

        if ($this->new->nullable || !empty($this->new->if)) {
            $column->nullable();
        }
    }
}