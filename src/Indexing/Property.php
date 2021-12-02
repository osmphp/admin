<?php

namespace Osm\Admin\Indexing;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
/**
 * @property Index $indexer
 * @property string $name #[Serialized]
 * @property string[] $parameters #[Serialized]
 * @property string[] $after #[Serialized]
 */
class Property extends Object_
{

}