<?php

namespace Osm\Admin\Schema;

use Osm\Core\Object_;
use Osm\Admin\Schema\Attributes\Unsigned;
use Osm\Admin\Schema\Attributes\Explicit;
use Osm\Admin\Schema\Attributes\AutoIncrement;
use Osm\Admin\Ui\Attributes\Control\Hidden;
use Osm\Admin\Ui\Attributes\Filter;

/**
 * @property int $id #[Explicit, Unsigned, AutoIncrement, Hidden, Filter\Id]
 * @property ?string $title
 *
 * @uses Unsigned, Explicit, AutoIncrement, Hidden, Filter\Id
 */
class Record extends Object_
{

}