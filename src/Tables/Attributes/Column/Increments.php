<?php

namespace Osm\Data\Tables\Attributes\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Schema\Property;
use Osm\Data\Tables\Attributes\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Increments extends Column
{
}