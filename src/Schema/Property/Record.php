<?php

namespace Osm\Admin\Schema\Property;

use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property string $on_delete #[Serialized]
 *
 * @uses Serialized
 */
#[Type('record')]
class Record extends Bag
{
    public const ON_DELETE_SET_NULL = 'set null';
    public const ON_DELETE_CASCADE = 'cascade';
    public const ON_DELETE_RESTRICT = 'restrict';

    protected function get_on_delete(): string {
        return static::ON_DELETE_SET_NULL;
    }
}