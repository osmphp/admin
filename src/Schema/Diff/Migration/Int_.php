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
 * @property Property\Int_ $property
 * @property \stdClass[]|StringSize[] $sizes
 */
class Int_ extends Migration
{
    protected function get_sizes(): array {
        return [
            PropertyObject::TINY => (object)[
                'sql_type' => 'tinyInteger',
                'min' => -0x80,
                'max' => 0x7F,
                'unsigned_max' => 0xFF,
            ],
            PropertyObject::SMALL => (object)[
                'sql_type' => 'smallInteger',
                'min' => -0x8000,
                'max' => 0x7FFF,
                'unsigned_max' => 0xFFFF,
            ],
            PropertyObject::MEDIUM => (object)[
                'sql_type' => 'integer',
                'min' => -0x80000000,
                'max' => 0x7FFFFFFF,
                'unsigned_max' => 0xFFFFFFFF,
            ],
            PropertyObject::LONG => (object)[
                'sql_type' => 'bigInteger',
                'min' => -0x8000000000000000,
                'max' => 0x7FFFFFFFFFFFFFFF,
                'unsigned_max' => 0xFFFFFFFFFFFFFFFF,
            ],
        ];
    }

    public bool $check_range = false;

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
        if ($this->table) {
            $this->logAttribute('size');
        }

        if (!$this->property->old) {
            $this->preSize();
            return;
        }

        if ($this->property->old->size === $this->property->new->size) {
            return;
        }

        if ($this->becomingSmaller()) {
            $this->checkRange('size');
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
            $this->run('size');
        }
    }

    protected function becomingSmaller(): bool {
        return
            array_search($this->property->old->size, array_keys($this->sizes)) >
            array_search($this->property->new->size, array_keys($this->sizes));
    }

    protected function checkRange(string $attr): void {
        if ($this->check_range) {
            return;
        }

        $this->check_range = true;

        if ($this->mode === Property::CONVERT) {
            if ($this->property->new->unsigned) {
                $min = 0;
                $max = $this->sizes[$this->property->new->size]->unsigned_max;
            }
            else {
                $min = $this->sizes[$this->property->new->size]->min;
                $max = $this->sizes[$this->property->new->size]->max;
            }

            $this->new_value = "IF({$this->new_value} > $max, $max, " .
                "IF({$this->new_value} < $min, $min, {$this->new_value}))";
            $this->run($attr);
        }
    }

    protected function unsigned(): void {
        if ($this->table) {
            $this->logAttribute('unsigned');
        }

        if (!$this->property->old) {
            $this->preUnsigned();
            return;
        }

        $this->preOldUnsigned();

        if ($this->property->old->actually_unsigned ===
            $this->property->new->actually_unsigned)
        {
            return;
        }

        $this->checkRange('unsigned');
        $this->postUnsigned();
    }

    protected function preUnsigned(): void {
        if ($this->mode == Property::CREATE ||
            $this->mode == Property::PRE_ALTER)
        {
            $this->setUnsigned();
        }
    }

    protected function postUnsigned(): void {
        if ($this->mode == Property::POST_ALTER) {
            $this->setUnsigned();
        }
    }

    protected function preOldUnsigned(): void {
        if ($this->mode == Property::CREATE ||
            $this->mode == Property::PRE_ALTER)
        {
            if ($this->column && $this->property->old->explicit) {
                if ($this->property->old->actually_unsigned) {
                    $this->column->unsigned();
                }
            }
        }
    }

    protected function setUnsigned(): void {
        if ($this->column) {
            if ($this->property->new->actually_unsigned) {
                $this->column->unsigned();
            }
            $this->run('unsigned');
        }
    }

    protected function autoIncrement(): void {
        if ($this->table) {
            $this->logAttribute('auto_increment');
        }

        if ($this->property->old &&
            $this->property->old->auto_increment !==
            $this->property->new->auto_increment)
        {
            throw new InvalidChange(__("'#[AutoIncrement]' attribute of the ':table.:property' can't be changed", [
                'property' => $this->property->new->name,
                'table' => $this->property->new->parent->table_name,
            ]));
        }

        if (!$this->property->new->auto_increment) {
            return;
        }

        if ($this->mode == Property::CREATE ||
            $this->mode == Property::PRE_ALTER)
        {
            if ($this->column) {
                $this->column->autoIncrement();
                $this->run('auto_increment');
            }
        }
    }
}