<?php

namespace Osm\Data\Scopes;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Attributes\Column;

/**
 * @property int $id #[
 *      Serialized,
 *      Column\Increments,
 * ]
 * @property ?int $parent_id #[
 *      Serialized,
 *      Column\Int_(unsigned: true, references: 'scopes.id', on_delete: 'cascade'),
 * ]
 */
#[Table('scopes')]
class Scope extends Object_
{
}