<?php

namespace Osm\Data\Scopes;

use Osm\Core\Attributes\Name;
use Osm\Data\Queries\Attributes\Of;
use Osm\Data\Tables\Query as TableQuery;

#[Name('scopes'), Of(Scope::class)]
class Scopes extends TableQuery
{

}