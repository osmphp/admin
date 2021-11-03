<?php

namespace Osm\Admin\Accounts;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Scopes\Attributes\Scoped;
use Osm\Admin\Tables\Traits\Id;
use Osm\Admin\Tables\Traits\Type;
use Osm\Admin\Tables\Attributes\Table;
use Osm\Admin\Tables\Attributes\Column;

#[Table('accounts'), Scoped]
class Account extends Object_
{
    use Id, Type;
}