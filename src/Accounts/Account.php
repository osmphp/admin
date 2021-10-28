<?php

namespace Osm\Data\Accounts;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Base\Traits\Types;

/**
 * @property int $id #[Serialized]
 */
class Account extends Object_
{
    use Types;
}