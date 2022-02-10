<?php

namespace Osm\Admin\Samples\Generics;

use Osm\Admin\Schema\Attributes\Table;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Attributes\Explicit;

/**
 * @property ?Recursive $parent #[Explicit]
 *
 * @uses Explicit
 */
#[Table('recursive_items')]
class Recursive extends Record
{

}