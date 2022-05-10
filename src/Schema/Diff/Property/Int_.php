<?php

namespace Osm\Admin\Schema\Diff\Property;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use function Osm\__;

#[Type('int')]
class Int_ extends Scalar
{
    protected function doMigrate(): array {
        return [
            $this->type(),
            $this->nullable(),
            $this->explicit(),
            $this->size(),
            $this->unsigned(),
            $this->autoIncrement(),
        ];
    }

    protected function column(?Blueprint $table): ?ColumnDefinition {
        if (!$table) {
            return null;
        }

        return match ($this->new->size) {
            PropertyObject::TINY => $table->tinyInteger($this->new->name),
            PropertyObject::SMALL => $table->smallInteger($this->new->name),
            PropertyObject::MEDIUM => $table->integer($this->new->name),
            PropertyObject::LONG => $table->bigInteger($this->new->name),
        };
    }

    protected function unsigned(string $mode, ?ColumnDefinition $column): bool {
        $changed = $mode === static::CREATE ||
            $this->old->actually_unsigned != $this->new->actually_unsigned;

        if ($this->new->actually_unsigned) {
            $column?->unsigned();
        }

        return match($mode) {
            static::CREATE => true,
            static::PRE_ALTER => $changed,
            static::POST_ALTER => false,
        };
    }

    public function convert(Query $query = null): bool {
        return false;
    }

    protected function get_non_null_formula(): string {
        return "0";
    }
}