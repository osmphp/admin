<?php

namespace Osm\Data\Accounts;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Base\Traits\Types;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Query;

/**
 * @property int $id #[Serialized]
 */
#[Table('accounts')]
class Account extends Object_
{
    use Types;
}