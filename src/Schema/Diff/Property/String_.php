<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Property\String_ as StringPropertyObject;
use Osm\Core\Attributes\Type;

#[Type('string')]
class String_ extends Scalar
{
    public function migrate(string $mode, Blueprint $table = null): bool {
        // if it's a new property, migration should run no matter what
        $run = $mode === static::CREATE;

        $column = $this->column($table);
        $run = $this->nullable($mode, $column) || $run;
        $this->change($mode, $column);

        return $run;
    }

    protected function column(?Blueprint $table): ?ColumnDefinition {
        if (!$table) {
            return null;
        }

        if ($this->new->max_length &&
            $this->new->max_length <= StringPropertyObject::VARCHAR_LENGTH)
        {
            return $table->string($this->new->name, $this->new->max_length);
        }

        return match ($this->new->size) {
            PropertyObject::TINY => $table->tinyText($this->new->name),
            PropertyObject::SMALL => $table->text($this->new->name),
            PropertyObject::MEDIUM => $table->mediumText($this->new->name),
            PropertyObject::LONG => $table->longText($this->new->name),
        };
    }
}