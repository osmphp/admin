<?php

namespace Osm\Admin\Samples\Orders;

use Osm\Admin\Schema\Attributes\Table;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Attributes\Explicit;

/**
 * @property Order $order #[Explicit]
 *
 * @uses Explicit
 */
#[Table('order_lines')]
class Line extends Record
{
}