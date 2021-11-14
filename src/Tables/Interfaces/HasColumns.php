<?php

namespace Osm\Admin\Tables\Interfaces;

use Osm\Admin\Tables\Column;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $name #[Serialized]
 * @property Column[] $columns #[Serialized]
 */
interface HasColumns
{

}