<?php

namespace Osm\Admin\Accounts;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Scoped;
use Osm\Admin\Base\Traits\Id;
use Osm\Admin\Base\Traits\Type;
use Osm\Admin\Base\Attributes\Table;

#[Table('accounts'), Scoped]
class Account extends Object_
{
    use Id, Type;
}