<?php

namespace Osm\Data\Samples\Orders;

use Osm\Core\Object_;
use Osm\Data\Scopes\Traits\ScopeId;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Traits\Id;
use Osm\Core\Attributes\Serialized;

/**
 *
 */
#[Table('orders')]
class Order extends Object_
{
    use Id, ScopeId;
}