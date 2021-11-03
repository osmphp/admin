<?php

namespace Osm\Admin\Base\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Table;
use Osm\Admin\Base\Attributes\Grid;

/**
 * @property int $id #[
 *      Serialized,
 *      Table\Increments,
 *      Grid\PrimaryKey(sort_order: 10),
 * ]
 */
trait Id
{

}