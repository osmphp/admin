<?php

namespace Osm\Admin\Samples\Orders;

use Osm\Core\Object_;
use Osm\Admin\Scopes\Traits\ScopeId;
use Osm\Admin\Tables\Attributes\Table;
use Osm\Admin\Tables\Traits\Id;
use Osm\Core\Attributes\Serialized;

/**
 *
 */
#[Table('orders')]
class Order extends Object_
{
    use Id, ScopeId;
}