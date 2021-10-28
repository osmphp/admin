<?php

namespace Osm\Data\Scopes;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Query as TableQuery;

/**
 * @property int $id #[Serialized]
 * @property int $parent_id #[Serialized]
 * @property string $name #[Serialized]
 */
#[Table('scopes')]
class Scope extends Object_
{
}