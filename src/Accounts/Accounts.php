<?php

namespace Osm\Data\Accounts;

use Osm\Core\Attributes\Name;
use Osm\Data\Queries\Attributes\Of;
use Osm\Data\Tables\Query;

#[Name('accounts'), Of(Account::class)]
class Accounts extends Query
{

}