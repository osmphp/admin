<?php

namespace Osm\Admin\Schema;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property int|string|bool $value #[Serialized]
 * @property string $title #[Serialized]
 *
 * @uses Serialized
 */
class Option extends Object_
{

}