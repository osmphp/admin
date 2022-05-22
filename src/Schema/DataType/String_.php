<?php

namespace Osm\Admin\Schema\DataType;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Schema\Hints\StringSize;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property \stdClass[]|StringSize[] $sizes
 */
#[Type('string')]
class String_ extends Scalar
{
    public function castToSql(Formula $formula, array &$bindings,
                              array &$from, string $join): string
    {
        return "CONCAT({$formula->toSql($bindings, $from, $join)})";
    }

    protected function get_sizes(): array {
        // `max_length` is approximate worst case calculated here:
        // https://stackoverflow.com/questions/13932750/tinytext-text-mediumtext-and-longtext-maximum-storage-sizes

        return [
            Property::TINY => (object)[
                'sql_type' => 'tinyText',
                'max_length' => 85,
            ],
            Property::SMALL => (object)[
                'sql_type' => 'text',
                'max_length' => 21845,
            ],
            Property::MEDIUM => (object)[
                'sql_type' => 'mediumText',
                'max_length' => 5592415,
            ],
            Property::LONG => (object)[
                'sql_type' => 'longText',
                'max_length' => 1431655765,
            ],
        ];
    }
}