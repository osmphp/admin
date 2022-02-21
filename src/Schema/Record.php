<?php

namespace Osm\Admin\Schema;

use Osm\Core\Object_;
use Osm\Admin\Schema\Attributes\Unsigned;
use Osm\Admin\Schema\Attributes\Explicit;
use Osm\Admin\Schema\Attributes\AutoIncrement;
use Osm\Admin\Ui\Attributes\Control\Hidden;
/**
 * @property int $id #[Explicit, Unsigned, AutoIncrement, Hidden]
 * @property ?string $title
 *
 * @uses Unsigned, Explicit, AutoIncrement, Hidden
 */
class Record extends Object_
{

}