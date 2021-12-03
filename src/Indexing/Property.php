<?php

namespace Osm\Admin\Indexing;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
/**
 * @property Indexer $indexer
 * @property string $name #[Serialized]
 * @property string[] $depends_on #[Serialized]
 * @property string[] $after #[Serialized]
 */
class Property extends Object_
{

}