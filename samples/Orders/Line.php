<?php

namespace Osm\Data\Samples\Orders;

use Osm\Core\Object_;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Traits\Id;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Tables\Attributes\Column;

/**
 * @property int $order_id #[
 *      Serialized,
 *      Column\Int_(unsigned: true, references: 'orders.id', on_delete: 'cascade'),
 * ]
 * @property ?int $product_id #[
 *      Serialized,
 *      Column\Int_(unsigned: true, references: 'products.id', on_delete: 'set null'),
 * ]
 */
#[Table('order_lines')]
class Line extends Object_
{
    use Id;
}