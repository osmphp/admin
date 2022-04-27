<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Core\Attributes\Type;

#[Type('int')]
class Int_ extends Scalar
{
    protected function create(Blueprint $table): void
    {
        $column = match ($this->new->size) {
            PropertyObject::TINY => $table->tinyInteger($this->new->name),
            PropertyObject::SMALL => $table->smallInteger($this->new->name),
            PropertyObject::MEDIUM => $table->integer($this->new->name),
            PropertyObject::LONG => $table->bigInteger($this->new->name),
        };

        if ($this->new->unsigned || $this->new->auto_increment) {
            $column->unsigned();
        }

        if ($this->new->auto_increment) {
            $column->autoIncrement();
        }

        if ($this->new->nullable || !empty($this->if)) {
            $column->nullable();
        }
    }
}