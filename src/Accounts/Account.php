<?php

namespace Osm\Data\Accounts;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Scopes\Attributes\Scoped;
use Osm\Data\Tables\Traits\Id;
use Osm\Data\Tables\Traits\Type;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Attributes\Column;

#[Table('accounts'), Scoped]
class Account extends Object_
{
    use Id, Type;
}