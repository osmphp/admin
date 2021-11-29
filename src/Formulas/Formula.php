<?php

namespace Osm\Admin\Formulas;

use Osm\Admin\Schema\Class_;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Formula $parent
 * @property string $text #[Serialized]
 */
class Formula extends Object_
{
    use SubTypes;
}