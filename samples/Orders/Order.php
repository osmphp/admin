<?php

namespace Osm\Admin\Samples\Orders;

use Osm\Core\Object_;
use Osm\Admin\Base\Traits\ScopeId;
use Osm\Admin\Base\Attributes\Storage;
use Osm\Admin\Base\Traits\Id;
use Osm\Core\Attributes\Serialized;

/**
 *
 */
#[Storage\Table('orders')]
class Order extends Object_
{
    use Id, ScopeId;
}