<?php

namespace Osm\Admin\Samples\Orders;

use Osm\Core\Object_;
use Osm\Admin\Base\Attributes\Table;
use Osm\Admin\Base\Traits\Id;
use Osm\Core\Attributes\Serialized;

/**
 * @property int $order_id #[
 *      Serialized,
 *      Table\Int_(unsigned: true, references: 'orders.id', on_delete: 'cascade'),
 * ]
 * @property ?int $product_id #[
 *      Serialized,
 *      Table\Int_(unsigned: true, references: 'products.id', on_delete: 'set null'),
 * ]
 */
#[Table('order_lines')]
class Line extends Object_
{
    use Id;
}