<?php

namespace Osm\Data\Scopes;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property int $id #[Serialized]
 * @property int $parent_id #[Serialized]
 * @property string $name #[Serialized]
 */
class Scope extends Object_
{

}