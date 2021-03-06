<?php

namespace Osm\Admin\Schema\Property;

use Osm\Admin\Schema\Property;
use Osm\Admin\Ui\Query;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property mixed $default #[Serialized]
 *
 * @uses Serialized
 */
class Scalar extends Property
{
    protected function get_default(): mixed {
        return null;
    }
}