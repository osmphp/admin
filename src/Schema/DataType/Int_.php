<?php

namespace Osm\Admin\Schema\DataType;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Hints\IntSize;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property \stdClass[]|IntSize[] $sizes
 */
#[Type('int')]
class Int_ extends Scalar
{
    public function castToSql(Formula $formula, array &$bindings,
        array &$from, string $join): string
    {
        $sql = $formula->toSql($bindings, $from, $join);

        if ($formula->data_type->type === 'string') {
            return "0 + {$sql}";
        }

        throw new NotImplemented($this);
    }

    public function safeCastToSql(Formula $formula, Formula $default,
        array &$bindings, array &$from, string $join): string
    {
        if ($formula->data_type->type === 'string') {
            return "IF(" .
                "({$formula->toSql($bindings, $from, $join)}) " .
                    "REGEXP '^[[:space:]]*[[:digit:]]+[[:space:]]*$', ".
                "0 + REGEXP_REPLACE({$formula->toSql($bindings, $from, $join)}, " .
                    "'(^[[:space:]]+|[[:space:]]+$)', ''), " .
                $default->toSql($bindings, $from, $join) .
            ")";
        }

        return parent::safeCastToSql($formula, $default, $bindings,
            $from, $join);
    }

    protected function get_sizes(): array {
        return [
            Property::TINY => (object)[
                'sql_type' => 'tinyInteger',
                'min' => -0x80,
                'max' => 0x7F,
                'unsigned_max' => 0xFF,
            ],
            Property::SMALL => (object)[
                'sql_type' => 'smallInteger',
                'min' => -0x8000,
                'max' => 0x7FFF,
                'unsigned_max' => 0xFFFF,
            ],
            Property::MEDIUM => (object)[
                'sql_type' => 'integer',
                'min' => -0x80000000,
                'max' => 0x7FFFFFFF,
                'unsigned_max' => 0xFFFFFFFF,
            ],
            Property::LONG => (object)[
                'sql_type' => 'bigInteger',
                'min' => -0x8000000000000000,
                'max' => 0x7FFFFFFFFFFFFFFF,
                'unsigned_max' => 0xFFFFFFFFFFFFFFFF,
            ],
        ];
    }
}