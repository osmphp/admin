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
        $this->truncate();
    }

    protected function define(Blueprint $table): ColumnDefinition {
        return $table->text($this->new->name);
    }

    protected function letDbToConvertData(): bool {
        // Trust MySql to do the data conversion
        return true;
    }

    protected function size(): void {
        $this->attribute('size', function() {
            $this->change(!$this->old ||
                $this->old->type !== $this->new->type ||
                $this->old->size !== $this->new->size);
            $newSize = $this->new->data_type->sizes[$this->new->size];

            if ($this->new->explicit) {
                $this->column(fn(?ColumnDefinition $column) =>
                    $column?->type($newSize->sql_type)
                );
            }
        });
    }

    /** @noinspection PhpUndefinedMethodInspection */
    protected function length(): void {
        $this->attribute('max_length', function() {
            $this->change(!$this->old ||
                $this->old->type !== $this->new->type ||
                ($this->old->max_length ?? null) !== $this->new->max_length);

            if ($this->new->explicit) {
                $this->column(fn(?ColumnDefinition $column) =>
                    $this->new->max_length
                        ? $column
                            ?->type('string')
                            ?->length($this->new->max_length)
                        : $column
                );
            }
        });
    }

    protected function truncate(): void {
        $this->attribute('truncate', function() {
            if (!$this->old) {
                return;
            }

            if (isset($this->change['type']) ||
                (
                    isset($this->change['size']) ||
                    isset($this->change['max_length'])
                ) && $this->becomingShorter())
            {
                $maxLength = $this->maxLength($this->new);

                $this->convert(fn(string $value) =>
                    "LENGTH({$value} ?? '') > $maxLength ? " .
                    "LEFT({$value}, $maxLength) : {$value}");
            }
        });
    }

    protected function maxLength(
        StringPropertyObject|\stdClass|null $property): int
    {
        if (!$property) {
            return 0;
        }

        if ($property->max_length ?? null) {
            return $property->max_length;
        }

        return $this->new->data_type->sizes[$property->size]->max_length;
    }

    protected function becomingShorter(): bool {
        return $this->maxLength($this->old) > $this->maxLength($this->new);
    }
}