<?php

namespace Osm\Admin\Schema;

use Osm\Core\Object_;
use Osm\Admin\Schema\Attributes\Unsigned;
use Osm\Admin\Schema\Attributes\Explicit;
use Osm\Admin\Schema\Attributes\AutoIncrement;
use Osm\Admin\Ui\Attributes\Control\Hidden;
use Osm\Admin\Ui\Attributes\Facet;

/**
 * @property int $id #[Explicit, Unsigned, AutoIncrement, Hidden, Facet\Id]
 * @property ?string $title
 *
 * @uses Unsigned, Explicit, AutoIncrement, Hidden, Facet\Id
 */
class Record extends Object_
{

}