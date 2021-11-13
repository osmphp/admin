<?php

namespace Osm\Admin\Accounts;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Traits\Id;
use Osm\Admin\Base\Traits\SubTypes;
use Osm\Admin\Base\Attributes\Storage;

#[Storage\ScopedTable('accounts')]
class Account extends Object_
{
    use Id, SubTypes;
}