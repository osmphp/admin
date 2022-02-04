<?php

namespace Osm\Admin\Schema;

use Osm\Core\Object_;
use Osm\Admin\Schema\Attributes\Unsigned;
use Osm\Admin\Schema\Attributes\Explicit;
use Osm\Admin\Schema\Attributes\AutoIncrement;

/**
 * @property int $id #[Explicit, Unsigned, AutoIncrement]
 */
class Record extends Object_
{

}