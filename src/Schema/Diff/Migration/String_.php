<?php

namespace Osm\Admin\Schema\Diff\Migration;

use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Schema\Diff\Migration;
use Osm\Admin\Schema\Diff\Property;
use Osm\Admin\Schema\Exceptions\InvalidChange;
use Osm\Admin\Schema\Hints\StringSize;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Schema\Property as PropertyObject;
use function Osm\__;

/**
 * @property Property\String_ $property
 * @property \stdClass[]|StringSize[] $sizes
 */
class String_ extends Migration
{
    protected bool $truncate = false;

    protected function get_sizes(): array {
        // `max_length` is approximate worst case calculated here:
        // https://stackoverflow.com/questions/13932750/tinytext-text-mediumtext-and-longtext-maximum-storage-sizes

        return [
            PropertyObject::TINY => (object)[
                'sql_type' => 'tinyText',
                'max_length' => 85,
            ],
            PropertyObject::SMALL => (object)[
                'sql_type' => 'text',
                'max_length' => 21845,
            ],
            PropertyObject::MEDIUM => (object)[
                'sql_type' => 'mediumText',
                'max_length' => 5592415,
            ],
            PropertyObject::LONG => (object)[
                'sql_type' => 'longText',
                'max_length' => 1431655765,
            ],
        ];
    }

    public function migrate(): void {
        $this->init();
        $this->explicit();
        $this->type();
        $this->nullable();
        $this->size();
        $this->length();
    }

    protected function column(): ColumnDefinition {
        return $this->table->text($this->property->new->name);
    }

    protected function size(): void {
        if ($this->property->new->max_length) {
            return;
        }

        if (!$this->property->old) {
            $this->preSize();
            return;
        }

        if ($this->property->old->size === $this->property->new->size) {
            return;
        }

        if ($this->becomingShorter()) {
            $this->truncate();
            $this->postSize();
        }
        else {
            $this->preSize();
        }
    }

    protected function preSize(): void {
        if ($this->mode == Property::CREATE ||
            $this->mode == Property::PRE_ALTER)
        {
            $this->setSize();
        }
    }

    protected function postSize(): void {
        if ($this->mode == Property::POST_ALTER) {
            $this->setSize();
        }
    }

    protected function setSize(): void {
        if ($this->column) {
            $this->column->type(
                $this->sizes[$this->property->new->size]->sql_type);
            $this->run = true;
        }
    }

    protected function becomingShorter(): bool {
        return $this->maxLength($this->property->old) >
            $this->maxLength($this->property->new);
    }

    protected function maxLength(
        PropertyObject\String_|\stdClass|null $property): int
    {
        if (!$property) {
            return 0;
        }

        if ($property->max_length) {
            return $property->max_length;
        }

        return $this->sizes[$property->size]->max_length;
    }

    protected function truncate(): void {
        if ($this->truncate) {
            return;
        }

        $this->truncate = true;

        if ($this->mode === Property::CONVERT) {
            $maxLength = $this->maxLength($this->property->new);

            $this->new_value =
                "IF(LENGTH({$this->new_value} ?? '') > $maxLength, " .
                "LEFT({$this->new_value}, $maxLength), {$this->new_value})";
            $this->run = true;
        }
    }

    protected function length(): void {
        if (!$this->property->new->max_length) {
            return;
        }

        if (!$this->property->old) {
            $this->preLength();
            return;
        }

        if ($this->property->old->max_length ===
            $this->property->new->max_length)
        {
            return;
        }

        if ($this->becomingShorter()) {
            $this->truncate();
            $this->postLength();
        }
        else {
            $this->preLength();
        }
    }

    protected function preLength(): void {
        if ($this->mode == Property::CREATE ||
            $this->mode == Property::PRE_ALTER)
        {
            $this->setSize();
        }
    }

    protected function postLength(): void {
        if ($this->mode == Property::POST_ALTER) {
            $this->setLength();
        }
    }

    protected function setLength(): void {
        if ($this->column) {
            $this->column->type('string');

            /** @noinspection PhpUndefinedMethodInspection */
            $this->column->length($this->property->new->max_length);

            $this->run = true;
        }
    }

}