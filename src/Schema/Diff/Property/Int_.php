<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Schema\DataType;
use Osm\Admin\Schema\Exceptions\InvalidChange;
use Osm\Core\Attributes\Type;
use Osm\Admin\Schema\Property\Int_ as IntPropertyObject;
use Osm\Core\Exceptions\NotImplemented;
use function Osm\__;

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
        $this->checkRange();
        $this->autoIncrement();
    }

    protected function define(Blueprint $table): ColumnDefinition {
        return $table->integer($this->new->name);
    }

    protected function size(): void {
        $this->attribute('size', function() {
            $this->change(!$this->old ||
                $this->old->size !== $this->new->size);
            $newSize = $this->new->data_type->sizes[$this->new->size];

            if ($this->new->explicit) {
                $this->column(fn(?ColumnDefinition $column) =>
                    $column->type($newSize->sql_type)
                );
            }
        });
    }

    protected function unsigned(): void {
        $this->attribute('unsigned', function() {
            $this->change(!$this->old ||
                $this->old->actually_unsigned !== $this->new->actually_unsigned);

            if ($this->new->explicit) {
                $this->column(fn(?ColumnDefinition $column) =>
                    $this->new->actually_unsigned
                        ? $column->unsigned()
                        : $column
                );
            }
        });
    }

    protected function autoIncrement(): void {
        $this->attribute('auto_increment', function() {
            if (isset($this->old->auto_increment) &&
                $this->old->auto_increment !== $this->new->auto_increment)
            {
                throw new InvalidChange(__("'#[AutoIncrement]' attribute of the ':table.:property' can't be changed", [
                    'property' => $this->new->name,
                    'table' => $this->new->parent->table_name,
                ]));
            }

            if ($this->new->explicit && $this->new->auto_increment) {
                $this->column(fn(?ColumnDefinition $column) =>
                    $column->autoIncrement()
                );
            }
        });
    }

    protected function checkRange(): void {
        if (!$this->old) {
            return;
        }

        if (isset($this->change['type']) ||
            isset($this->change['size']) && $this->becomingSmaller() ||
            isset($this->change['unsigned']))
        {
            $newSize = $this->new->data_type->sizes[$this->new->size];

            if ($this->new->unsigned) {
                $min = 0;
                $max = $newSize->unsigned_max;
            }
            else {
                $min = $newSize->min;
                $max = $newSize->max;
            }

            $this->convert(fn(string $value) =>
                "{$value} > $max ? $max : ({$value} < $min ? $min : {$value})");
        }
    }

    protected function becomingSmaller(): bool {
        $sizes = $this->new->data_type->sizes;

        return
            array_search($this->old->size, array_keys($sizes)) >
            array_search($this->new->size, array_keys($sizes));
    }
}